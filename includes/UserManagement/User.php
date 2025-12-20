<?php
namespace PHPizza\UserManagement;
use DateTime;
use PHPizza\COPPACompliance\COPPADatabase;

class User {
    const YES = true;
    const NO = false;

    public $id;
    public $username;
    private $userdb;
    private $coppadb;

    public function __construct($id, $username) {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->id = $id;
        $this->username = $username;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->coppadb = new COPPADatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
        ];
    }


    public function getUsername(): string {
        return $this->username;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPasswordHash(): string {
        return $this->userdb->getPasswordHashByUsername($this->username);
    }

    public function can_I_do(string $action, string|bool|array|null $context = null) {
        return $this->userdb->can_user_do($this->id, $action, $context);
    }

    public function when_was_I_born(): ?DateTime {
        return $this->userdb->get_date_of_birth_by_userid($this->id);
    }

    public function am_I_a_child(): bool {
        // Check if the user is a child under COPPA by checking if their birthdate was less than 13 years ago
        $now = new DateTime();
        $birthdate = $this->when_was_I_born();
        $diff = $now->diff($birthdate);
        return $diff->y < 13;
    }

    public function am_I_blocked(): bool {
        // First, check if the user is explicitly blocked
        $blocked = $this->userdb->is_user_blocked($this->id);

        // Second, if they are a child, check if tey have an approved COPPA consent request
        if ($this->am_I_a_child()) {
            $consentRequest = $this->coppadb->get_coppa_consent_request_by_child($this->id);
            if ($consentRequest && $consentRequest->getConsentStatus() === COPPAConsentRequest::YES) {
                return false;
            }
        }
        return $blocked;
    }
}