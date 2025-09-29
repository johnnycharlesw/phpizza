use anyhow::Context;
use bytes::Bytes;
use clap::Parser;
use hyper::service::{make_service_fn, service_fn};
use hyper::{Body, Request, Response, Server, StatusCode};
use std::convert::Infallible;
use std::net::SocketAddr;
use std::path::{Path, PathBuf};
use std::process::Stdio;
use tokio::io::{AsyncReadExt, AsyncWriteExt};
use tokio::process::Command;
use tokio::net::TcpListener;
use tokio_rustls::TlsAcceptor;
use tokio_rustls::rustls::{self, Certificate, PrivateKey, ServerConfig};
use futures::StreamExt;
use hyper::server::conn::Http;
use std::sync::Arc;

#[derive(Parser, Debug)]
#[command(author, version, about = "phpizza Rust CGI server (dev-friendly)")]
struct Opt {
    /// Port to bind (default 8080)
    #[arg(short, long, default_value = "8080")]
    port: u16,

    /// Document root (default: project root)
    #[arg(short, long, default_value = "../")]
    docroot: String,

    /// Path to php executable (php-cgi or php)
    #[arg(long, default_value = "php")]
    php_bin: String,

    /// Optional TLS certificate PEM file
    #[arg(long)]
    tls_cert: Option<String>,

    /// Optional TLS private key PEM file
    #[arg(long)]
    tls_key: Option<String>,
}

async fn handle_request(req: Request<Body>, docroot: PathBuf, php_bin: String) -> Result<Response<Body>, Infallible> {
    // Map path to script file
    let uri_path = req.uri().path();
    let mut script_path = if uri_path == "/" { 
        docroot.join("index.php")
    } else {
        // prevent directory traversal: join then canonicalize and verify prefix
        let candidate = docroot.join(&uri_path.trim_start_matches('/'));
        candidate
    };

    // If path is a directory, try index.php inside it
    if script_path.is_dir() {
        script_path = script_path.join("index.php");
    }

    // canonicalize may fail if file doesn't exist; we'll check existence
    if !script_path.exists() {
        let mut not_found = Response::new(Body::from("404 Not Found"));
        *not_found.status_mut() = StatusCode::NOT_FOUND;
        return Ok(not_found);
    }

    // Spawn php-cgi with CGI env vars
    // Build env
    let mut cmd = Command::new(&php_bin);
    cmd.arg("-f").arg(script_path.as_os_str());
    // pass request body to stdin
    cmd.stdout(Stdio::piped()).stderr(Stdio::piped()).stdin(Stdio::piped());

    // Set some minimal CGI env vars
    let method = req.method().as_str().to_string();
    let query = req.uri().query().unwrap_or("").to_string();
    cmd.env("REQUEST_METHOD", method);
    cmd.env("SCRIPT_FILENAME", script_path.to_string_lossy().to_string());
    cmd.env("QUERY_STRING", query);
    cmd.env("DOCUMENT_ROOT", docroot.to_string_lossy().to_string());
    cmd.env("SERVER_SOFTWARE", "phpizza-rust/0.1");

    // Content-Length and BODY
    let whole_body = hyper::body::to_bytes(req.into_body()).await.unwrap_or_else(|_| Bytes::new());
    if !whole_body.is_empty() {
        cmd.env("CONTENT_LENGTH", whole_body.len().to_string());
    }

    match cmd.spawn() {
        Ok(mut child) => {
            // write body to stdin
            if let Some(mut stdin) = child.stdin.take() {
                let body_clone = whole_body.clone();
                tokio::spawn(async move {
                    let _ = stdin.write_all(&body_clone).await;
                    let _ = stdin.shutdown().await;
                });
            }

            let output = child.wait_with_output().await.context("failed to wait for php process");
            match output {
                Ok(out) => {
                    // php-cgi typically outputs HTTP headers then body separated by blank line.
                    let stdout = String::from_utf8_lossy(&out.stdout);
                    // split headers and body
                    if let Some(pos) = stdout.find("\r\n\r\n") {
                        let headers = &stdout[..pos];
                        let body = &stdout[pos + 4..];
                        // parse Status: header if present
                        let mut status = StatusCode::OK;
                        for line in headers.lines() {
                            if line.to_ascii_lowercase().starts_with("status:") {
                                if let Some(code_str) = line.split(':').nth(1) {
                                    if let Ok(code) = code_str.trim().split(' ').next().unwrap_or("").parse::<u16>() {
                                        if let Ok(sc) = StatusCode::from_u16(code) { status = sc; }
                                    }
                                }
                            }
                        }
                        let mut resp = Response::new(Body::from(body.to_string()));
                        *resp.status_mut() = status;
                        // Copy selected headers (Content-Type)
                        for line in headers.lines() {
                            if let Some((k, v)) = line.split_once(':') {
                                let key = k.trim();
                                let val = v.trim();
                                if !key.eq_ignore_ascii_case("status") {
                                    if key.eq_ignore_ascii_case("content-type") {
                                        resp.headers_mut().insert(hyper::header::CONTENT_TYPE, hyper::header::HeaderValue::from_str(val).unwrap_or_else(|_| hyper::header::HeaderValue::from_static("text/plain")));
                                    }
                                    // add other headers as needed
                                }
                            }
                        }
                        return Ok(resp);
                    } else {
                        // no headers present, just return stdout
                        let mut resp = Response::new(Body::from(stdout.to_string()));
                        *resp.status_mut() = StatusCode::OK;
                        return Ok(resp);
                    }
                }
                Err(e) => {
                    let mut resp = Response::new(Body::from(format!("500 Internal Server Error: {}", e)));
                    *resp.status_mut() = StatusCode::INTERNAL_SERVER_ERROR;
                    return Ok(resp);
                }
            }
        }
        Err(e) => {
            let mut resp = Response::new(Body::from(format!("500 Internal Server Error: failed to spawn php: {}", e)));
            *resp.status_mut() = StatusCode::INTERNAL_SERVER_ERROR;
            return Ok(resp);
        }
    }
}

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    let opt = Opt::parse();

    let addr = SocketAddr::from(([0, 0, 0, 0], opt.port));
    let docroot = Path::new(&opt.docroot).canonicalize().unwrap_or_else(|_| PathBuf::from(&opt.docroot));
    let php_bin = opt.php_bin.clone();

    let make_svc = make_service_fn(move |_conn| {
        let docroot = docroot.clone();
        let php_bin = php_bin.clone();
        async move {
            Ok::<_, Infallible>(service_fn(move |req| {
                handle_request(req, docroot.clone(), php_bin.clone())
            }))
        }
    });

    if let (Some(cert_pem), Some(key_pem)) = (opt.tls_cert.clone(), opt.tls_key.clone()) {
        // Try to load cert and key
        let certs = load_certs(&cert_pem).context("failed to load certs")?;
        let key = load_private_key(&key_pem).context("failed to load private key")?;

        let mut config = ServerConfig::builder()
            .with_safe_defaults()
            .with_no_client_auth()
            .with_single_cert(certs, key)
            .context("invalid cert/key")?;
        config.alpn_protocols = vec![b"h2".to_vec(), b"http/1.1".to_vec()];
        let acceptor = TlsAcceptor::from(Arc::new(config));

        println!("Listening on https://{} , docroot={} , php={}", addr, docroot.display(), php_bin);

        let listener = TcpListener::bind(addr).await.context("failed to bind address")?;
        let service_make = make_svc;

        loop {
            let (socket, peer) = listener.accept().await.context("accept failed")?;
            let acceptor = acceptor.clone();
            let svc = service_make.clone();
            let docroot = docroot.clone();
            let php_bin = php_bin.clone();

            tokio::spawn(async move {
                match acceptor.accept(socket).await {
                    Ok(tls_stream) => {
                        let service = service_fn(move |req: Request<Body>| {
                            handle_request(req, docroot.clone(), php_bin.clone())
                        });
                        if let Err(e) = Http::new().serve_connection(tls_stream, service).await {
                            eprintln!("Error serving connection: {}", e);
                        }
                    }
                    Err(e) => {
                        eprintln!("TLS accept error from {}: {}", peer, e);
                    }
                }
            });
        }
    } else {
        let server = Server::bind(&addr).serve(make_svc);

        println!("Listening on http://{} , docroot={} , php={}", addr, docroot.display(), php_bin);
        server.await.context("server error")?;
    }
    Ok(())
}

fn load_certs(path: &str) -> anyhow::Result<Vec<Certificate>> {
    let data = std::fs::read(path)?;
    let mut rdr = std::io::BufReader::new(&*data);
    let certs = rustls_pemfile::certs(&mut rdr)?;
    Ok(certs.into_iter().map(Certificate).collect())
}

fn load_private_key(path: &str) -> anyhow::Result<PrivateKey> {
    let data = std::fs::read(path)?;
    let mut rdr = std::io::BufReader::new(&*data);
    // try rsa or pkcs8
    let keys = rustls_pemfile::pkcs8_private_keys(&mut rdr)?;
    if !keys.is_empty() {
        return Ok(PrivateKey(keys[0].clone()));
    }
    // retry from start
    let mut rdr2 = std::io::BufReader::new(&*data);
    let keys = rustls_pemfile::rsa_private_keys(&mut rdr2)?;
    if !keys.is_empty() {
        return Ok(PrivateKey(keys[0].clone()));
    }
    anyhow::bail!("no private key found in {}", path)
}
