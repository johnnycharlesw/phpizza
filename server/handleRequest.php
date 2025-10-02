<?php
// Minimal handler executed as: php server/handleRequest.php METHOD PATH_OR_PATH::BODY::BASE64
// Designed to be called from the thread-based Python dev server in this repo.

ini_set('display_errors', '0');
error_reporting(0);

$method = $argv[1] ?? 'GET';
$raw = $argv[2] ?? '/';

$body = '';
// Support PATH::BODY::base64encoded for POST body
$body_marker = '::BODY::';
if (strpos($raw, $body_marker) !== false) {
    list($path, $b64) = explode($body_marker, $raw, 2);
    $path = $path === '' ? '/' : $path;
    $body = base64_decode($b64);
} else {
    $path = $raw;
}

// Normalize path and extract query
$u = parse_url($path);
$req_path = $u['path'] ?? '/';
$query = $u['query'] ?? '';

$docroot = realpath(__DIR__ . '/..');
if ($docroot === false) {
    echo "Error: unable to determine document root";
    exit(1);
}

// Map request path to a filesystem path
$candidate = $docroot . $req_path;
// If path ends with /, try index.php
if (is_dir($candidate)) {
    $candidate = rtrim($candidate, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'index.php';
}

// If file doesn't exist, fallback to front controller index.php in docroot
if (!file_exists($candidate)) {
    $candidate = $docroot . DIRECTORY_SEPARATOR . 'index.php';
    if (!file_exists($candidate)) {
        echo "404 Not Found";
        exit(0);
    }
}

$ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));

// Serve static files directly (non-php)
if ($ext !== 'php') {
    $type = mime_content_type($candidate) ?: 'application/octet-stream';
    // For the dev server, we just print the content and let the caller set headers
    echo file_get_contents($candidate);
    exit(0);
}

// For PHP files, execute with a separate PHP process so typical web-style runtime applies
$php_bin = getenv('PHP_CGI_BIN') ?: (defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : null);
// If PHP_BINARY points to php.exe (cli), prefer php-cgi if it's available in PATH
if (!$php_bin) {
    // try common names
    $which = function($name) {
        $paths = explode(PATH_SEPARATOR, getenv('PATH') ?: '');
        foreach ($paths as $p) {
            $full = $p . DIRECTORY_SEPARATOR . $name;
            if (is_file($full) && is_executable($full)) return $full;
        }
        return false;
    };
    $php_bin = $which('php-cgi') ?: $which('php') ?: 'php';
}

// Build environment for the child PHP process (minimal set)
$env = $_ENV;
$env['REQUEST_METHOD'] = $method;
$env['REQUEST_URI'] = $path;
$env['QUERY_STRING'] = $query;
$env['DOCUMENT_ROOT'] = $docroot;
$env['SCRIPT_FILENAME'] = $candidate;
$env['SERVER_SOFTWARE'] = 'php-dev-server/1';
if ($body !== '') {
    $env['CONTENT_LENGTH'] = strlen($body);
}

$descriptors = [
    0 => ["pipe", "r"], // stdin
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"]  // stderr
];

$cmd = [$php_bin, '-f', $candidate];

$process = proc_open($cmd, $descriptors, $pipes, $docroot, $env);
if (!is_resource($process)) {
    echo "Error: failed to start php process";
    exit(1);
}

// Write body to stdin if present
if ($body !== '') {
    fwrite($pipes[0], $body);
}
fclose($pipes[0]);

$stdout = stream_get_contents($pipes[1]);
fclose($pipes[1]);

    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    // Try to get proc status before closing
    $pstatus = null;
    if (function_exists('proc_get_status')) {
        $pstatus = proc_get_status($process);
    }

    $status = proc_close($process);

    if ($status !== 0) {
    // Log diagnostics to stderr (so the server/operator can see them) but don't expose to the client
    $diag = [
        'php_bin' => $php_bin,
        'script' => $candidate,
        'status' => $status,
        'stderr' => $stderr,
    ];
        if ($pstatus !== null) {
            $diag['proc_get_status'] = $pstatus;
        }
    // Attempt to fetch proc_get_status if available
    error_log("[php-handler] child process failed: " . json_encode($diag));
    // Return a generic error page to the client
    echo "<h1>500 Internal Server Error</h1>\n<p>Handler failed to execute server-side script.</p>";
    exit(0);
}

// Print stdout directly. The Python server will attach Content-Type and status.
echo $stdout;

exit(0);
