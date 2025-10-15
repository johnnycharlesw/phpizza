<?php
namespace PHPizza;
class User {
    public $id;
    public $username;
    public $is_admin;
    private $userdb;

    public function __construct($id, $username, $is_admin) {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->id = $id;
        $this->username = $username;
        $this->is_admin = $is_admin;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'is_admin' => $this->is_admin,
        ];
    }

    public function isAdmin(): bool {
        return $this->is_admin;
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
}