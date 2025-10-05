#!/usr/bin/env python3
import http.server as pyhttp
import ssl
import os
import subprocess
import base64
import sys
import shutil
from socketserver import ThreadingMixIn

php_mode = True

def get_ssl_context(certfile: str, keyfile: str) -> ssl.SSLContext:
    # Create a TLS context (modern TLS) and load cert/key
    context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
    context.load_cert_chain(certfile, keyfile)
    # Lower security level if needed for older test certs
    try:
        context.set_ciphers("@SECLEVEL=1:ALL")
    except Exception:
        # Not all OpenSSL builds support set_ciphers with @SECLEVEL
        pass
    return context


class ThreadingHTTPServer(ThreadingMixIn, pyhttp.HTTPServer):
    daemon_threads = True


class PHPHTTPRequestHandler(pyhttp.SimpleHTTPRequestHandler):
    def do_GET(self):
        if php_mode:
            output, rc, stderr, php_bin = self.call_php(req_type="GET", path=self.path)
            # Parse possible CGI-style headers from the PHP stdout
            status = 200
            headers = {}
            body = output if isinstance(output, str) else str(output)
            # Try CRLF CRLF first, fall back to LF LF
            split_seq = "\r\n\r\n"
            pos = body.find(split_seq)
            if pos == -1:
                split_seq = "\n\n"
                pos = body.find(split_seq)
            if pos != -1:
                hdrs = body[:pos]
                body = body[pos + len(split_seq):]
                for line in hdrs.splitlines():
                    if not line.strip():
                        continue
                    if ':' in line:
                        k, v = line.split(':', 1)
                        k = k.strip()
                        v = v.strip()
                        if k.lower() == 'status':
                            # Status: 404 Not Found
                            try:
                                code = int(v.split()[0])
                                status = code
                            except Exception:
                                pass
                        else:
                            headers[k] = v
            # If child process returned an error and no Status header was set, set 500
            if rc is not None and rc != 0 and status == 200:
                status = 500
            # Build response
            self.send_response(status)
            # copy headers from CGI output if present, otherwise default
            if 'Content-Type' in headers:
                self.send_header('Content-Type', headers['Content-Type'])
            else:
                self.send_header('Content-Type', 'text/html; charset=utf-8')
            self.send_header('Content-Length', str(len(body.encode('utf-8'))))
            # Add other headers (excluding hop-by-hop) -- minimal set
            for hk, hv in headers.items():
                if hk.lower() in ('content-type', 'content-length', 'status'):
                    continue
                try:
                    self.send_header(hk, hv)
                except Exception:
                    pass
            self.end_headers()
            # Log stderr to server console (truncate) if child failed
            if rc is not None and rc != 0 and stderr:
                print(f"[php stderr] (php={php_bin}) {stderr[:1000]}")
            # Write body (bytes)
            try:
                self.wfile.write(body.encode('utf-8'))
            except (ConnectionAbortedError, BrokenPipeError) as e:
                # client disconnected; log and move on
                print(f"client disconnected during write: {e}")
        else:
            return super().do_GET()

    def do_HEAD(self):
        if php_mode:
            # Call handler and set status based on return code
            _, rc, stderr, php_bin = self.call_php(req_type="HEAD", path=self.path)
            status = 200
            if rc is not None and rc != 0:
                status = 500
                if stderr:
                    print(f"[php stderr] (php={php_bin}) {stderr[:1000]}")
            self.send_response(status)
            self.send_header("Content-Type", "text/html; charset=utf-8")
            self.end_headers()
        else:
            return super().do_HEAD()

    def call_php(self, req_type: str, path: str) -> str:
        # Build command to call the PHP handler script
        # Handler script expected at ./server/handleRequest.php
        handler = os.path.join(os.path.dirname(__file__), 'server', 'handleRequest.php')
        if not os.path.exists(handler):
            return ("", -1, f"handler not found: {handler}", 'php')

        # For POST, path may include encoded body
        try:
            # Prefer php-cgi if available, otherwise fallback to php
            php_bin = shutil.which('php-cgi') or shutil.which('php') or 'php'
            # Use list form to avoid shell injection
            result = subprocess.run([php_bin, handler, req_type, path], capture_output=True, text=True, check=False)
            # Return tuple: stdout, returncode, stderr, php_bin
            return (result.stdout or '', result.returncode, result.stderr or '', php_bin)
        except FileNotFoundError:
            return ("", -1, "php executable not found in PATH", 'php')

    def do_POST(self):
        content_length = int(self.headers.get("Content-Length", 0))
        post_data = self.rfile.read(content_length) if content_length > 0 else b""
        # base64 encode to safely pass binary data via argv to PHP handler
        encoded = base64.b64encode(post_data).decode('ascii')
        # Pass path and encoded body as separate args
        output, rc, stderr, php_bin = self.call_php(req_type="POST", path=self.path + "::BODY::" + encoded)
        # reuse the same header parsing logic as GET
        status = 200
        headers = {}
        body = output if isinstance(output, str) else str(output)
        split_seq = "\r\n\r\n"
        pos = output.find(split_seq)
        if pos == -1:
            split_seq = "\n\n"
            pos = body.find(split_seq)
        if pos != -1:
            hdrs = output[:pos]
            body = body[pos + len(split_seq):]
            for line in hdrs.splitlines():
                if not line.strip():
                    continue
                if ':' in line:
                    k, v = line.split(':', 1)
                    k = k.strip()
                    v = v.strip()
                    if k.lower() == 'status':
                        try:
                            code = int(v.split()[0])
                            status = code
                        except Exception:
                            pass
                    else:
                        headers[k] = v
        if rc is not None and rc != 0 and status == 200:
            status = 500
        self.send_response(status)
        if 'Content-Type' in headers:
            self.send_header('Content-Type', headers['Content-Type'])
        else:
            self.send_header('Content-Type', 'text/html; charset=utf-8')
        self.send_header('Content-Length', str(len(body.encode('utf-8'))))
        for hk, hv in headers.items():
            if hk.lower() in ('content-type', 'content-length', 'status'):
                continue
            try:
                self.send_header(hk, hv)
            except Exception:
                pass
        self.end_headers()
        # Log stderr if process failed
        if rc is not None and rc != 0 and stderr:
            print(f"[php stderr] (php={php_bin}) {stderr[:1000]}")
        try:
            self.wfile.write(body.encode('utf-8'))
        except (ConnectionAbortedError, BrokenPipeError) as e:
            print(f"client disconnected during write: {e}")


def run_servers(http_port=8080, https_port=8443, certfile=None, keyfile=None):
    webdir = os.getcwd()

    server_address = ('127.0.0.1', http_port)
    httpd = ThreadingHTTPServer(server_address, PHPHTTPRequestHandler)

    print(f"Serving HTTP on {server_address[0]} port {server_address[1]} (http://{server_address[0]}:{server_address[1]}/) ...")

    # Start HTTPS server in separate thread if cert/key provided
    if certfile and keyfile:
        https_address = ('127.0.0.1', https_port)
        httpsd = ThreadingHTTPServer(https_address, PHPHTTPRequestHandler)
        context = get_ssl_context(certfile, keyfile)
        httpsd.socket = context.wrap_socket(httpsd.socket, server_side=True)
        import threading

        def serve_https():
            print(f"Serving HTTPS on {https_address[0]} port {https_address[1]} (https://{https_address[0]}:{https_address[1]}/) ...")
            httpsd.serve_forever()

        t = threading.Thread(target=serve_https, daemon=True)
        t.start()

    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print('\nShutting down')
        httpd.shutdown()


if __name__ == '__main__':
    cert = None
    key = None
    if len(sys.argv) >= 3:
        cert = sys.argv[1]
        key = sys.argv[2]
    run_servers(certfile=cert, keyfile=key)
