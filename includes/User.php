<?php
namespace PHPizza;
class User {
    const YES = true;
    const NO = false;

    public $id;
    public $username;
    private $userdb;

    public function __construct($id, $username) {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->id = $id;
        $this->username = $username;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
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

    public function can_I_do(string $action) {
        return $this->userdb->can_user_do($this->id, $action);
    }
}