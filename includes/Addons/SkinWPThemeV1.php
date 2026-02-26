<?php
namespace PHPizza\Addons;

/**
 * WordPress Theme V1 algorithm, reimplemented in PHPizza Core
 *
 * This skin variant attempts to load component parts from PHP templates in the
 * skin folder following common WordPress naming conventions, with a safe
 * fallback to the core Markdown-based parts implementation.
 */
class SkinWPThemeV1 extends Skin
{
    public function __construct(string $name) {
        parent::__construct($name);
    }

    /**
     * Public entry that overrides the base to allow PHP template parts in
     * addition to the default Markdown parts. This mimics WP V1 theme behavior
     * where header.php, footer.php, sidebar.php, etc. are PHP templates.
     */
    public function get_component(string $type)
    {
        $vars = $this->get_template_variables_as_array();

        // 1) Try WordPress-like PHP template parts first
        $phpCandidates = [
            "/{$type}.php",
            "/parts/{$type}.php",
            "/template-parts/{$type}.php",
            "/partials/{$type}.php",
            "/{$type}.phtml",
            "/parts/{$type}.phtml",
        ];

        foreach ($phpCandidates as $rel) {
            $path = $this->assetPath($rel);
            if (is_file($path)) {
                return $this->renderPhpTemplate($path, $vars);
            }
        }

        // 2) Fallback to parent implementation (Markdown parts in /parts/*.md)
        return parent::get_component($type);
    }

    /**
     * Render a PHP template and return its buffered output.
     * All template variables are made available as individual variables via
     * extract().
     */
    private function renderPhpTemplate(string $absPath, array $vars): string
    {
        // Provide common variables typical for WP themes, mapped from PHPizza
        // variables, while preserving original keys for compatibility.
        $compat = [
            'blogname' => $vars['sitename'] ?? null,
            'language' => $vars['siteLanguage'] ?? null,
            'stylesheet_directory' => $this->getFolderPath(),
            'template_directory' => $this->getFolderPath(),
            'siteTheme' => $vars['siteTheme'] ?? null,
        ];

        // Merge: user vars take precedence, then add our compatibility vars
        $locals = array_merge($compat, $vars);

        // Isolate scope and buffer output
        $renderer = function($__phpizza_template_path, $__phpizza_vars) {
            extract($__phpizza_vars, EXTR_SKIP);
            ob_start();
            try {
                include $__phpizza_template_path;
            } finally {
                return ob_get_clean();
            }
        };

        return $renderer($absPath, $locals);
    }

    private function convert_theme_json_to_phpizza_skin_json(): void {
        // Convert theme.json to PHPizza skin.json
        $skinJsonPath = $this->assetPath('/skin.json');
        $themeJsonPath = $this->assetPath('/theme.json');
        $skinJson = json_decode(file_get_contents($themeJsonPath), true);
        $themeJson = json_decode(file_get_contents($themeJsonPath), true);
        $skinJson['name'] = $this->manifest['name'] || $themeJson['name'] || $this->name ?? 'AutoPortedWordPressTheme';
        $skinJson['description'] = $this->manifest['description'] ?? $themeJson['description'] ?? $this->description ?? 'AutoPorted WordPress Theme';
        $skinJson['author'] = $this->manifest['author'] ?? $themeJson['author'] ?? $this->author ?? 'Site Owner';
        $skinJson['version'] = $this->manifest['version'] ?? $themeJson['version'] ?? '1.0.0';
        $skinJson['homepage'] = $this->manifest['homepage'] ?? $themeJson['homepage'] ?? $this->homepage ?? 'index.php';
        $skinJson['tags'] = $this->manifest['tags'] ?? $themeJson['tags'] ?? [];
        $skinJson['icon'] = $this->manifest['icon'] ?? $themeJson['icon'] ?? null;
        $skinJson['stylesheets'] = $this->manifest['stylesheets'] ?? $themeJson['stylesheets'] ?? [];
        $skinJson['scripts'] = $this->manifest['scripts'] ?? $themeJson['scripts'] ?? [];
        file_put_contents($skinJsonPath, json_encode($skinJson, JSON_PRETTY_PRINT) . "\n");
    }

    /**
     * Convert the theme to a native PHPizza skin by materializing Markdown
     * parts from existing PHP templates. This will:
     * - Look for header, footer, sidebar PHP templates in common locations
     * - Create /parts/*.md files if missing, embedding the current rendered
     *   HTML of those templates so the theme becomes self-contained as a
     *   PHPizza-native skin
     * - Leave original PHP templates untouched (non-destructive)
     */
    public function convert_to_phpizza_native_skin(): void
    {
        // Create parts directory if it doesn't exist
        $partsDir = $this->assetPath('/parts');
        if (!is_dir($partsDir)) {
            @mkdir($partsDir, 0775, true);
        }

        // Back up skin.json and theme.json before converting
        $skinJsonPath = $this->assetPath('/skin.json');
        $themeJsonPath = $this->assetPath('/theme.json');
        if (file_exists($skinJsonPath)) {
            copy($skinJsonPath, $skinJsonPath . '.bak');
        }
        if (file_exists($themeJsonPath)) {
            copy($themeJsonPath, $themeJsonPath . '.bak');
        }

        // Convert theme.json to PHPizza skin.json and SCSS stylesheets
        $this->convert_theme_json_to_phpizza_skin_json();

        // Convert PHP templates

        $targets = ['header', 'sidebar', 'footer', 'single', 'page', '404', 'front-page', 'home', 'search', 'attachment', 'archive']; // JUST DEDUPLICATING
        foreach ($targets as $type) {
            $mdPath = $this->assetPath("/parts/{$type}.md");
            // Skip if already native
            if (is_file($mdPath)) {
                continue;
            }

            // Try to render from existing PHP template
            $phpCandidates = [
                "/{$type}.php",
                "/parts/{$type}.php",
                "/template-parts/{$type}.php",
                "/partials/{$type}.php",
                "/{$type}.phtml",
                "/parts/{$type}.phtml",
            ];

            $rendered = null;
            foreach ($phpCandidates as $rel) {
                $path = $this->assetPath($rel);
                if (is_file($path)) {
                    $rendered = $this->renderPhpTemplate($path, $this->get_template_variables_as_array());
                    break;
                }
            }

            // If nothing to convert, skip this part
            if ($rendered === null) {
                continue;
            }

            // Wrap rendered HTML inside a minimal Markdown fence to preserve as-is
            $markdown = "\n<!-- Converted from WP V1 template: {$type} -->\n\n" . $rendered . "\n";
            @file_put_contents($mdPath, $markdown);
        }
    }
}
