<?php
namespace PHPizza;

class CLIEntryPoint
{
    public function __construct()
    {
        // CLI-specific initialization if needed
    }

    public function run()
    {
        
        // Check for updates and install updates if available
        $updater = new Updater();
        $updater->install_updates_if_available();

        // Main CLI logic
        global $argv;
        $cmd = isset($argv[1]) ? $argv[1] : 'help';
        switch ($cmd) {
            case 'list':
                $this->listMaintenance();
                break;
            case 'run':
                $script = isset($argv[2]) ? $argv[2] : '';
                if (!$script) {
                    echo "Usage: php index.php run <script.php>\n";
                    exit(1);
                }
                $this->runMaintenance($script);
                break;
            case 'simulate-browser-request':
                $entry = new \PHPizza\BrowserEntryPoint();
                $entry->run();
                break;
            case 'help':
            default:
                echo "php index.php <command>\n";
                echo "Commands:\n";
                echo "  list                List available maintenance scripts in maintenance/\n";
                echo "  run <script.php>    Execute a maintenance script from maintenance/\n";
                echo "  help                Show this help\n";
                break;
        }
    }

    private function listMaintenance()
    {
        $files = glob(__DIR__ . '/../maintenance/*.php');
        foreach ($files as $f) {
            echo basename($f) . "\n";
        }
    }

    private function runMaintenance(string $script)
    {
        // Sanitize script name: must be a basename (no slashes)
        $base = basename($script);
        $path = realpath(__DIR__ . '/../maintenance/' . $base);
        $maintenanceDir = realpath(__DIR__ . '/../maintenance');
        if ($path === false || strpos($path, $maintenanceDir) !== 0) {
            echo "Script not found or invalid: $script\n";
            exit(1);
        }

        // Execute via PHP CLI to ensure environment is correct
        $php = PHP_BINARY;
        $cmd = escapeshellarg($php) . ' ' . escapeshellarg($path);
        passthru($cmd, $exitCode);
        return $exitCode;
    }
}
