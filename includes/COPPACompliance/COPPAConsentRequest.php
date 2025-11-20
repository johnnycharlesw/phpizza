<?php
namespace PHPizza\COPPACompliance;

class COPPAConsentRequest {
    private $parent_user_id;
    private $child_user_id;
    private $consent_type;
    private $consent_status;
    private $coppadb;
    public $id;

    const YES = true;
    const NO = false;

    public function __construct($id, $parent_user_id, $child_user_id, $consent_type, $consent_status) {
        // Initialize the COPPA database

        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->coppadb = new COPPADatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);

        // Set the parent user ID, child user ID, consent type, and consent status
        $this->parent_user_id = $parent_user_id;
        $this->child_user_id = $child_user_id;
        $this->consent_type = $consent_type;
        $this->consent_status = $consent_status;
        $this->id=$id;
    }

    public function approve_consent() {
        $this->coppadb->approve_consent($this->id);
    }
}