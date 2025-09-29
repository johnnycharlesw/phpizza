# phpizza Rust CGI server (scaffold)

This scaffold provides a minimal Rust-based HTTP server that dispatches requests to `php-cgi`/`php.exe` per-request using the CGI model. It is intended for development and low-traffic usage — production deployments should use PHP-FPM + FastCGI.

Features in scaffold
- Binds to a configurable port (default 8080)
- Maps incoming URIs to files under the document root
- Spawns `php`/`php-cgi` per-request and returns the resulting headers/body
- Minimal header parsing (Status and Content-Type)

Limitations
- TLS is not implemented in this scaffold. You can front it with a reverse proxy (Caddy, nginx) for TLS, or extend the scaffold to use rustls.
- No advanced header handling, no header normalization, no request pooling. Meant as a starting point.

Building (Windows PowerShell)

Install Rust: https://rustup.rs/ (use default toolchain)

From the project root (`c:\xampp\htdocs\phpizza`):

```powershell
cd rust-server
cargo build --release
```

Run (example):

```powershell
# Bind to 8080, docroot is repository root (default), php binary from PATH
.\target\release\phpizza-server.exe --port 8080 --docroot .. --php-bin php
```

Smoke test (PowerShell):

```powershell
Invoke-WebRequest http://localhost:8080 -UseBasicParsing
```

Enabling TLS later
- The scaffold contains CLI flags `--tls-cert` and `--tls-key` but the example listener does not wire rustls yet. For production TLS, either:
  - Implement HTTPS using `hyper-rustls` in the scaffold (I can add this), or
  - Run behind a reverse proxy that handles TLS (recommended for simplicity).

Next steps (I can do one for you):
- Implement TLS in the scaffold using `rustls` and `hyper-rustls`.
- Replace per-request spawning with a FastCGI client talking to PHP-FPM (Linux/prod option).
- Add concurrency limits and timeouts.
