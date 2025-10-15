<?php

namespace PHPizza;

/**
 * APIEntryPoint
 *
 * Wraps BrowserEntryPoint but returns JSON responses instead of echoing full HTML pages.
 * The JSON payload will include at least: status (http status code) and html (rendered HTML).
 */
class APIEntryPoint extends BrowserEntryPoint {
    public function __construct() {
        parent::__construct();
    }

    public function run_begin() {
        // Do the beginning part of function run()
        global $homepageName;
        $page_id = isset($_GET['title']) ? $_GET['title'] : $homepageName;
        $is_editor = isset($_GET['editing']) ? (bool)$_GET['editing'] && $_GET["editing"]==="true" : false;

        // Temporarily silence direct error output so the JSON is clean
        $this->prev_display_errors = ini_get('display_errors');
        $this->prev_error_reporting = error_reporting();
        $this->prev_error_log = ini_get('error_log');
        $tmpErr = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpizza_api_errors.log';
        ini_set('display_errors', '0');
        error_reporting(0);

        // Redirect error_log to a temp file to avoid stderr output during API run
        ini_set('error_log', $tmpErr);
        return [$page_id, $is_editor];
    }

    public function run_end($data) {
        // Do the ending part of function run()
        // Restore previous PHP error settings
        ini_set('display_errors', $this->prev_display_errors);
        error_reporting($this->prev_error_reporting);
        ini_set('error_log', $this->prev_error_log ?: '');

        $payload = [
            'status' => $data['status'],
            'title' => $data['title'],
            'description' => $data['description'],
            'keywords' => $data['keywords'],
            'html' => $data['html'],
        ];

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($data['status']);
        }

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function run() {
        // Build page data using the new BrowserEntryPoint helper so we get structured data
        global $homepageName;
        $page_id = isset($_GET['title']) ? $_GET['title'] : $homepageName;
        $is_editor = isset($_GET['editing']) ? (bool)$_GET['editing'] && $_GET["editing"]==="true" : false;

        // Temporarily silence direct error output so the JSON is clean
        $prev_display_errors = ini_get('display_errors');
        $prev_error_reporting = error_reporting();
        $prev_error_log = ini_get('error_log');
        $tmpErr = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpizza_api_errors.log';
        ini_set('display_errors', '0');
        error_reporting(0);

        // Redirect error_log to a temp file to avoid stderr output during API run
        ini_set('error_log', $tmpErr);

        $data = $this->buildPageData($page_id, $is_editor);

        // Restore previous PHP error settings
        ini_set('display_errors', $prev_display_errors);
        error_reporting($prev_error_reporting);
        ini_set('error_log', $prev_error_log ?: '');

        $payload = [
            'status' => $data['status'],
            'title' => $data['title'],
            'description' => $data['description'],
            'keywords' => $data['keywords'],
            'html' => $data['html'],
        ];

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($data['status']);
        }

        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}