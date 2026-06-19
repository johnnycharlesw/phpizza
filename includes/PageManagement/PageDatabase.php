<?php
namespace PHPizza\PageManagement;
use PHPizza\Database\Database;
use PHPizza\Exception;

class PageDatabase {
    private $db;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        if ($this->db->get_table_exists('pages') === false) {
            $this->install_my_table();
            //throw new Exception("The 'pages' table could not be found. Please update the database using the schema file.", 1);
            
        } else {
            $this->update_my_table();
        }
    }

    public function update_my_table(){
        global $dbType;
        $schemaPath = __DIR__ . "/../../sql/schema/$dbType/tables/pages/update.sql";
        $this->db->execute(file_get_contents($schemaPath));
        
    }

    private function install_my_table(){
        $this->db->create_table('pages');
        $this->update_my_table();
        $defaultPagesPath = __DIR__ . "/../../sql/schema/default_data/pages";
        $defaultPages = scandir($defaultPagesPath);
        foreach ($defaultPages as $defaultPage) {
            $defaultPageContent = file_get_contents($defaultPagesPath . '/' . $defaultPage);
            $defaultPageTitle = preg_replace("\.md", "", $defaultPage);
            $this->createPage($defaultPageTitle, $defaultPageContent);
        };
    }

    public function isSpecialPage($page_id){
        global $specialPrefix;
        preg_replace("/\/.+\.php/", ".php", $page_id);
        preg_replace("/\/.+/", "", $page_id);
        $specialPrefix = $specialPrefix ?? 'PHPizza:';
        $specialLen = strlen($specialPrefix);
        $hasSpecialPrefix = (function_exists('str_starts_with') && str_starts_with($page_id, $specialPrefix)) || (strpos($page_id, $specialPrefix) === 0);
        $hasSpecialSuffix = str_ends_with($page_id, '.php');
        return $hasSpecialPrefix || $hasSpecialSuffix;
    }

    public function get_special_page_id($page_id) {
        global $specialPrefix;
        $special_page_id = $page_id;
        $hasSpecialPrefix = (function_exists('str_starts_with') && str_starts_with($page_id, $specialPrefix)) || (strpos($page_id, $specialPrefix) === 0);
        $hasSpecialSuffix = str_ends_with($page_id, '.php');
        if ($hasSpecialPrefix) {
            $specialLen = strlen($specialPrefix);
            $special_page_id = substr($special_page_id, $specialLen);
        }
        if ($hasSpecialSuffix) {
            $special_page_id = substr($special_page_id, 0, -4);
        }
        return $special_page_id;
    }

    public function getPage($page_id) {
        // Accept numeric IDs or string titles/slugs
        if (is_numeric($page_id)) {
            $query = "SELECT * FROM pages WHERE id = ?";
            return $this->db->fetchRow($query, [$page_id]);
        }
        // Support pseudo-IDs like "id:123"
        $prefixId = 'id:';
        $prefixLen = strlen($prefixId);
        if ((function_exists('str_starts_with') && str_starts_with($page_id, $prefixId)) || (strpos($page_id, $prefixId) === 0)) {
            $id = substr($page_id, $prefixLen);
            if (is_numeric($id)) {
                $query = "SELECT * FROM pages WHERE id = ?";
                return $this->db->fetchRow($query, [$id]);
            }
        }
        // Support special pages prefixed with "PHPizza:Name". Strip the prefix (exact match) and look up mapping.
        if ($this->isSpecialPage($page_id)) {
            $candidate = $this->get_special_page_id($page_id);
            $candidate = trim($candidate);
            $path = $candidate;
            global $specialPageClassMap;
            while (!isset($specialPageClassMap[$candidate])) {
                if (preg_match('/.+\/.+/',$candidate)) {
                    preg_replace("/\/.+/", "", $candidate);
                } else {
                    return [
                        "title" => "Special Page $candidate not found",
                        "content" => "The special page you were looking for was not found."
                    ];
                }
            }
            if (!empty($specialPageClassMap) && isset($specialPageClassMap[$candidate])){
                $className = $specialPageClassMap[$candidate];
                if (class_exists($className)){
                    $specialPageInstance = new $className($path, $candidate, "");
                    if (method_exists($specialPageInstance, "getSpecialPageData")){
                        return $specialPageInstance->getSpecialPageData();
                    }
                }
            }
        }
        $query = "SELECT * FROM pages WHERE title = ?";
        return $this->db->fetchRow($query, [$page_id]);
    }

    public function getAllPages() {
        $query = "SELECT * FROM pages";
        return $this->db->fetchAll($query);
    }

    public function createPage($title, $content) {
        $query = "INSERT INTO pages (title, content) VALUES (?, ?)";
        return $this->db->execute($query, [$title, $content]);
    }

    public function updatePage($title, $content) {
        $query = "UPDATE pages SET content = ? WHERE title = ?";
        return $this->db->execute($query, [$content, $title]);
    }

    public function deletePage($title) {
        $query = "DELETE FROM pages WHERE title = ?";
        return $this->db->execute($query, [$title]);
    }

    public function renamePage($oldTitle, $newTitle){
        $query = "UPDATE pages SET title = ? WHERE title = ?";
        return $this->db->execute($query, [$newTitle, $oldTitle]);
    }
}