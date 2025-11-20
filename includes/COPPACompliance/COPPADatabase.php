<?php
namespace PHPizza\COPPACompliance;
use PHPizza\Database\Database;


class COPPADatabase {
    private $db;

    const YES = true;
    const NO = false;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        if ($this->db->get_table_exists('coppa_consents') === false) {
            throw new Exception("The 'coppa_consents' table could not be found. Please update the database using the schema file.", 1);
        }
        if ($this->db->get_table_exists('coppa_consent_requests') === false) {
            throw new Exception("The 'coppa_consent_requests' table could not be found. Please update the database using the schema file.", 1);
        }
    }
    
    public function create_coppa_consent_request($parent_userid, $child_userid, $consent_type, $consent_status) {
        // Insert a new record into the 'coppa_consent_requests' table
        $query = "INSERT INTO coppa_consent_requests (parent_userid, child_userid, consent_type, consent_status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->execute($query, [$parent_userid, $child_userid, $consent_type, $consent_status]);
        if ($stmt === false || $stmt === 0) {
            throw new Exception("Failed to create COPPA consent request. Please try again.", 1);
        }
        return $this->db->getLastInsertId();
    }

    public function get_coppa_consent_request($request_id) {
        // Retrieve a record from the 'coppa_consent_requests' table by ID
        $query = "SELECT * FROM coppa_consent_requests WHERE id = ?";
        $stmt = $this->db->fetchRow($query, [$request_id]);
        if ($stmt && is_array($stmt) && isset($stmt['id'])) {
            return new COPPAConsentRequest((int)$stmt['id'], (int)$stmt['parent_userid'], (int)$stmt['child_userid'], $stmt['consent_type'], $stmt['consent_status']);
        }
        return null;
    }

    public function update_coppa_consent_request($request_id, $consent_status) {
        // Update the consent status of a record in the 'coppa_consent_requests' table
        $query = "UPDATE coppa_consent_requests SET consent_status = ? WHERE id = ?";
        $stmt = $this->db->execute($query, [$consent_status, $request_id]);
        if ($stmt === false || $stmt === 0) {
            throw new Exception("Failed to update COPPA consent request. Please try again.", 1);
        }
        return true;
    }

    public function get_coppa_consent_request_by_child(int $id){
        // Retrieve a record from the 'coppa_consent_requests' table by child user ID
        $query = "SELECT * FROM coppa_consent_requests WHERE child_userid = ?";
        $stmt = $this->db->fetchAll($query, [$id]);
        if ($stmt && is_array($stmt)) {
            return array_map(function($row) {
                return new COPPAConsentRequest((int)$row['id'], (int)$row['parent_userid'], (int)$row['child_userid'], $row['consent_type'], $row['consent_status']);
            }, $stmt);
        }
        return null;
    }

    public function approve_consent($request_id) {
        // Approve a COPPA consent request
        $this->update_coppa_consent_request($request_id, COPPAConsentRequest::YES);
    }

    
}