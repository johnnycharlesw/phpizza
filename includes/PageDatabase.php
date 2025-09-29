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