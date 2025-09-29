import http.server as pyhttp
import ssl
import os
import subprocess
import base64
import http.server as pyhttp
import ssl
import os
import subprocess
import base64
php_mode=True
def get_ssl_context(certfile,keyfile):
    context = ssl.Context(ssl.TLSv1_2_METHOD)
    context.load_cert_chain(certfile, keyfile)
    context.set_ciphers("@SECLEVEL=1:ALL")
    return context

srvaddr_https=("127.0.0.1",443443)
srvaddr_http=("127.0.0.1",8080)

class PHPHTTPRequestHandler(pyhttp.SimpleHTTPRequestHandler):
    def do_GET(self):
        if php_mode:
            return subprocess.getoutput('php server/handleRequest.php GET '+self.path)
        else:
            return super().do_GET()
    
    def do_HEAD(self):
        if php_mode:
            return self.call_php(req_type="HEAD",path=self.path)
        else:
            return super().do_HEAD()
    
    def call_php(self,req_type, path):
        return subprocess.getoutput( 'php server/handleRequest.php '+req_type+' '+path )

    def do_POST(self):
        content_length = int(self.headers["Content-Length"])
        post_data = self.rfile.read(content_length)
        post_data_utf8=post_data.decode("utf-8")

        return self.call_php(req_type="POST",path=self.path+" "+base64.b64encode(post_data_utf8))