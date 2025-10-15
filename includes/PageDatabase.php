<?php
namespace PHPizza;

class PageDatabase {
    private $db;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
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
        $specialPrefix = 'PHPizza:';
        $specialLen = strlen($specialPrefix);
        if ((function_exists('str_starts_with') && str_starts_with($page_id, $specialPrefix)) || (strpos($page_id, $specialPrefix) === 0)) {
            $candidate = substr($page_id, $specialLen);
            $candidate = trim($candidate);
            global $specialPageClassMap;
            if (!empty($specialPageClassMap) && isset($specialPageClassMap[$candidate])){
                $className = $specialPageClassMap[$candidate];
                if (class_exists($className)){
                    $specialPageInstance = new $className();
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

    public function updatePage($page_id, $title, $content) {
        $query = "UPDATE pages SET title = ?, content = ? WHERE id = ?";
        return $this->db->execute($query, [$title, $content, $page_id]);
    }

    public function deletePage($page_id) {
        $query = "DELETE FROM pages WHERE id = ?";
        return $this->db->execute($query, [$page_id]);
    }
}