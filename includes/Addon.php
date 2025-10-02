<?php
namespace PHPizza;

/**
 * Base class for add-ons (extensions and skins).
 *
 * Centralizes common logic such as:
 * - Resolving the add-on folder path (../extensions/{name} or ../skins/{name})
 * - Loading and parsing the manifest JSON
 * - Basic validation and name normalization
 * - Utility to resolve asset paths
 */
abstract class Addon
{
    protected string $name;
    protected string $type; // 'extension' | 'skin'
    protected string $folderPath;
    protected ?array $manifest = null;

    /**
     * @param string $name The add-on name (folder name). Will be normalized.
     * @param string $type Either 'extension' or 'skin'
     */
    public function __construct(string $name, string $type)
    {
        $type = strtolower(trim($type));
        if (!in_array($type, ['extension', 'skin'], true)) {
            throw new \InvalidArgumentException("Invalid addon type: {$type}");
        }

        $this->name = self::normalizeName($name);
        $this->type = $type;

        // Project root (one level above includes/)
        $baseDir = dirname(__DIR__);
        $this->folderPath = $baseDir . DIRECTORY_SEPARATOR . $this->type . 's' . DIRECTORY_SEPARATOR . $this->name;
    }

    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getFolderPath(): string { return $this->folderPath; }
    public function getManifest(): ?array { return $this->manifest; }

    /**
     * Whether the add-on folder exists on disk.
     */
    public function exists(): bool
    {
        return is_dir($this->folderPath);
    }

    /**
     * Resolve the manifest path. Supports two conventions:
     * - {type}.json (e.g., extension.json or skin.json)
     * - manifest.json
     * Returns null if not found.
     */
    public function getManifestPath(): ?string
    {
        $candidates = [
            $this->folderPath . DIRECTORY_SEPARATOR . $this->type . '.json',
            $this->folderPath . DIRECTORY_SEPARATOR . 'manifest.json',
        ];
        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    /**
     * Load and parse the manifest JSON into an associative array.
     * Returns the parsed manifest, or null if not found (unless $strict is true).
     *
     * @throws \RuntimeException when file is missing in strict mode or JSON is invalid.
     */
    public function parse_manifest_json(bool $strict = false): ?array
    {
        $path = $this->getManifestPath();
        if (!$path) {
            if ($strict) {
                throw new \RuntimeException("Manifest file not found for {$this->type} '{$this->name}' in {$this->folderPath}");
            }
            return null;
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new \RuntimeException("Unable to read manifest at {$path}");
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in manifest at {$path}: " . json_last_error_msg());
        }

        $this->manifest = $data;
        return $data;
    }

    /**
     * Resolve an asset path within the add-on folder.
     */
    public function assetPath(string $relative): string
    {
        $relative = ltrim($relative, "\\/");
        return $this->folderPath . DIRECTORY_SEPARATOR . $relative;
    }

    /**
     * Normalize an add-on name to a filesystem-safe slug.
     */
    public static function normalizeName(string $name): string
    {
        $normalized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
        return trim($normalized, '-_');
    }
}
