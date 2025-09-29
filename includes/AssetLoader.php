<?php
namespace PHPizza;

/**
 * AssetLoader: serves CSS/JS assets with safety and caching.
 * - Resolves only within allowed directories (skins/, extensions/, assets/).
 * - Supports combining multiple files: ?f=path/a.css,path/b.css
 * - Emits Cache-Control, ETag, Last-Modified, and handles 304 responses.
 * - Optional naive minification for CSS (disabled by default).
 */
class AssetLoader
{
    /** @var string[] */
    private array $allowedRoots;

    /** @var int default max-age (seconds) */
    private int $defaultMaxAge = 3600;

    public function __construct(?array $allowedRoots = null)
    {
        $root = dirname(__DIR__); // project root
        $roots = [
            $root . DIRECTORY_SEPARATOR . 'skins',
            $root . DIRECTORY_SEPARATOR . 'extensions',
            $root . DIRECTORY_SEPARATOR . 'assets', // optional, if you add a shared assets dir
        ];
        // keep only existing dirs to avoid useless checks
        $this->allowedRoots = array_values(array_filter($roots, 'is_dir'));
        if ($allowedRoots) {
            // allow injection of custom roots for testing
            $this->allowedRoots = $allowedRoots;
        }
    }

    /**
     * Serve assets based on query string parameters.
     * Query params:
     * - f: comma-separated relative paths under allowed roots (e.g., skins/default/style.css)
     * - v: version string (appended to ETag)
     * - min: 1 to enable naive CSS minification (CSS only)
     */
    public function fromQuery(string $type): void
    {
        $filesParam = isset($_GET['f']) ? (string)$_GET['f'] : '';
        $version    = isset($_GET['v']) ? (string)$_GET['v'] : '';
        $minify     = isset($_GET['min']) && $_GET['min'] == '1';

        $paths = [];
        if ($filesParam !== '') {
            foreach (explode(',', $filesParam) as $rel) {
                $rel = trim($rel);
                if ($rel === '') { continue; }
                $paths[] = $rel;
            }
        }

        $this->serve($type, $paths, [
            'version' => $version,
            'minify'  => $minify,
        ]);
    }

    /**
     * Serve one or more files of the given type.
     * - $type: 'css' or 'js'
     * - $relativePaths: relative paths under allowed roots
     * - options: ['version' => string, 'minify' => bool, 'max_age' => int]
     */
    public function serve(string $type, array $relativePaths, array $options = []): void
    {
        $type = strtolower($type);
        if (!in_array($type, ['css', 'js'], true)) {
            $this->emitError(400, 'Unsupported asset type');
            return;
        }

        // Sanitize and resolve files
        $resolved = $this->resolveFiles($relativePaths, $type);
        if (empty($resolved)) {
            $this->emitError(404, 'No assets found');
            return;
        }

        // Compute caching metadata
        $version = (string)($options['version'] ?? '');
        $maxAge  = (int)($options['max_age'] ?? $this->defaultMaxAge);
        [$lastMod, $etag] = $this->computeCacheKeys($resolved, $version);

        // Conditional request handling (If-None-Match / If-Modified-Since)
        if ($this->isNotModified($etag, $lastMod)) {
            $this->emitNotModified($etag, $lastMod, $maxAge, $type);
            return;
        }

        // Read and concatenate contents
        $content = $this->concatFiles($resolved);

        // Optional naive CSS minification
        $minify = (bool)($options['minify'] ?? false);
        if ($minify && $type === 'css') {
            $content = $this->minifyCss($content);
        }

        // Emit response
        $this->emitContent($type, $content, $etag, $lastMod, $maxAge);
    }

    /**
     * Resolve and validate relative paths under allowed roots.
     * Only allow files with the expected extension and safe characters.
     * @return array<int, string> absolute paths
     */
    private function resolveFiles(array $relativePaths, string $type): array
    {
        $out = [];
        $ext = $type === 'css' ? '.css' : '.js';
        foreach ($relativePaths as $rel) {
            if (!$this->isSafeRelative($rel)) { continue; }
            if (strtolower(substr($rel, -strlen($ext))) !== $ext) { continue; }
            foreach ($this->allowedRoots as $root) {
                $abs = realpath($root . DIRECTORY_SEPARATOR . $rel);
                if ($abs && is_file($abs) && $this->isWithin($abs, $root)) {
                    $out[] = $abs;
                    break;
                }
            }
        }
        return array_values(array_unique($out));
    }

    private function isSafeRelative(string $rel): bool
    {
        if ($rel === '') return false;
        // Disallow directory traversal and control chars
        if (strpos($rel, "..") !== false) return false;
        // Allow common safe chars: letters, numbers, underscore, dash, slash, dot
        return (bool)preg_match('~^[A-Za-z0-9_./\-]+$~', $rel);
    }

    private function isWithin(string $path, string $root): bool
    {
        $root = rtrim(str_replace('\\', '/', $root), '/') . '/';
        $path = str_replace('\\', '/', $path);
        return strncmp($path, $root, strlen($root)) === 0;
    }

    /** @return array{0:int,1:string} [lastModified, etag] */
    private function computeCacheKeys(array $files, string $version): array
    {
        $last = 0;
        $parts = [];
        foreach ($files as $f) {
            $mtime = @filemtime($f) ?: 0;
            $size  = @filesize($f) ?: 0;
            $last = max($last, $mtime);
            $parts[] = $f . '|' . $mtime . '|' . $size;
        }
        if ($version !== '') {
            $parts[] = 'v=' . $version;
        }
        $etag = 'W/"' . sha1(implode('\n', $parts)) . '"';
        return [$last, $etag];
    }

    private function isNotModified(string $etag, int $lastMod): bool
    {
        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            return true;
        }
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
        if ($ifModifiedSince) {
            $since = strtotime($ifModifiedSince) ?: 0;
            if ($lastMod > 0 && $since >= $lastMod) {
                return true;
            }
        }
        return false;
    }

    private function emitNotModified(string $etag, int $lastMod, int $maxAge, string $type): void
    {
        $this->emitCommonHeaders($type, $etag, $lastMod, $maxAge);
        http_response_code(304);
    }

    private function concatFiles(array $files): string
    {
        $buf = '';
        foreach ($files as $f) {
            $buf .= file_get_contents($f) ?: '';
            $buf .= "\n";
        }
        return $buf;
    }

    private function minifyCss(string $css): string
    {
        // Very naive minifier: remove comments, collapse whitespace.
        // Do not use for complex CSS minification in production.
        $css = preg_replace('~/\*.*?\*/~s', '', $css) ?? $css; // comments
        $css = preg_replace('/\s+/', ' ', $css) ?? $css;        // whitespace
        $css = str_replace([' ;', '; '], ';', $css);
        $css = str_replace([' {', '{ '], '{', $css);
        $css = str_replace([' }', '} '], '}', $css);
        return trim($css);
    }

    private function emitContent(string $type, string $content, string $etag, int $lastMod, int $maxAge): void
    {
        $this->emitCommonHeaders($type, $etag, $lastMod, $maxAge);
        header('Content-Length: ' . (string)strlen($content));
        echo $content;
    }

    private function emitCommonHeaders(string $type, string $etag, int $lastMod, int $maxAge): void
    {
        if (headers_sent()) { return; }
        if ($type === 'css') {
            header('Content-Type: text/css; charset=UTF-8');
        } else {
            header('Content-Type: application/javascript; charset=UTF-8');
        }
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=' . $maxAge);
        if ($lastMod > 0) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastMod) . ' GMT');
        }
        header('ETag: ' . $etag);
    }

    private function emitError(int $code, string $message): void
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $message;
    }
}
