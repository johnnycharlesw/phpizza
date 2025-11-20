<?php
namespace PHPizza\UserManagement;

use DateTime;
use PHPizza\Database\Database;

class UserDatabase {
    // Not using PDO, instead using the Database class
    private Database $db;
    private UserGroupDatabase $groupdb;

    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->groupdb = new UserGroupDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }
    public function get_user_by_username(string $username): ?User {
        $row = $this->db->fetchRow("SELECT * FROM users WHERE username = ?", [$username]);
        if ($row && is_array($row) && isset($row['id'])) {
            return new User((int)$row['id'], $row['username'], $row['password_hash'], (bool)$row['is_admin']);
        }
        return null;
    }
    public function get_user_by_id(int $id): ?User {
        $row = $this->db->fetchRow("SELECT * FROM users WHERE id = ?", [$id]);
        if ($row && is_array($row) && isset($row['id'])) {
            return new User((int)$row['id'], $row['username'], $row['password_hash'], (bool)$row['is_admin']);
        }
        return null;
    
    }

    public function create_user(string $username, string $password): ?User {
        $existingUser = $this->get_user_by_username($username);
        if ($existingUser) {
            // Add activated account check later, if account already exists but is deactivated, reactivate it instead of creating a new one
            return null; // Username already exists
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $rows = $this->db->execute("INSERT INTO users (username, password_hash) VALUES (?, ?)", [$username, $password_hash]);
        if ($rows === false || $rows === 0) {
            return null;
        }
        $newUserId = $this->db->getLastInsertId();
        return new User((int)$newUserId, $username, $password_hash);
    }

    public function get_date_of_birth_by_userid(int $id): ?DateTime {
        $row = $this->db->fetchRow("SELECT date_of_birth FROM users WHERE id = ?", [$id]);
        if ($row && is_array($row) && isset($row['date_of_birth'])) {
            return new DateTime($row['date_of_birth']);
        }
        return null;
    }

    public function is_user_blocked(int $id): bool {
        // Check if the user is blocked by checking if their is_blocked field is set to 1
        $row = $this->db->fetchRow("SELECT is_blocked FROM users WHERE id = ?", [$id]);
        if ($row && is_array($row) && isset($row['is_blocked'])) {
            return $row['is_blocked'] === 1;
        }
        return false;
    }

    public function update_user_password(int $id, string $newPassword): bool {
        $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $rowsAffected = $this->db->execute("UPDATE users SET password_hash = ? WHERE id = ?", [$password_hash, $id]);
        return $rowsAffected > 0;
    }

    // Cannot delete accounts, the db is relationally linked to COPPA consents
    public function deactivate_user(int $id): bool {
        $rowsAffected = $this->db->execute("UPDATE users SET is_active = 0 WHERE id = ?", [$id]);
        return $rowsAffected > 0;
    }

    public function activate_user(int $id): bool {
        $rowsAffected = $this->db->execute("UPDATE users SET is_active = 1 WHERE id = ?", [$id]);
        return $rowsAffected > 0;
    }

    public function list_users(int $limit = 100, int $offset = 0): array {
        return $this->db->fetchAll("SELECT * FROM users LIMIT ? OFFSET ?", [$limit, $offset]);
    }

    public function verify_user_credentials(string $username, string $password): bool {
        $user = $this->get_user_by_username($username);
        if ($user) {
            return password_verify($password, $user->getPasswordHash());
        }
        return false;
    }

    public function get_user_by_email(string $email): ?User {
        $row = $this->db->fetchRow("SELECT * FROM users WHERE email = ?", [$email]);
        if ($row && is_array($row) && isset($row['id'])) {
            return new User((int)$row['id'], $row['username'], $row['password_hash']);
        }
        return null;
    }

    public function update_user_email(int $id, string $newEmail): bool {
        $rowsAffected = $this->db->execute("UPDATE users SET email = ? WHERE id = ?", [$newEmail, $id]);
        return $rowsAffected > 0;
    }

    public function get_user_settings(string $username): ?array {
        // Relational Database setup: userid in row of user_settings table refers to id in users table
        $user = $this->get_user_by_username($username);
        if (!$user) {
            return null;
        }
        $row = $this->db->fetchRow("SELECT * FROM user_settings WHERE userid = ?", [$user->getId()]);
        if ($row && is_array($row)) {
            return $row;
        }
        return null;
    }

    public function get_user_groups(string $username): ?array {
        // user_groups row has name, members, id, permissions, but not userid
        $user = $this->get_user_by_username($username);
        if (!$user) {
            return null;
        }
        # This is not UserGroupDatabase, but I put in an instance of UserGroupDatabase for simplicity, $this->groupdb
        $userGroups = $this->groupdb->get_user_groups_by_user_id($user->getId());
        if ($userGroups && is_array($userGroups)) {
            return $userGroups;
        }
        return null;
    }

    public function getPasswordHashByUsername(string $username): ?string {
        $row = $this->db->fetchRow("SELECT password_hash FROM users WHERE username = ?", [$username]);
        if ($row && is_array($row) && isset($row['password_hash'])) {
            return $row['password_hash'];
        }
        return null;
    }

    public function can_user_do(string $userId, string $action): bool {
        // This is not UserGroupDatabase, but I put in an instance of UserGroupDatabase for simplicity, $this->groupdb
        $userGroups = $this->groupdb->get_user_groups_by_user_id($userId);
        if ($userGroups && is_array($userGroups)) {
            foreach ($userGroups as $group) {
                if (in_array($action, $group->getPermissions())) {
                    return true;
                }
            }
        }
        return false;
    }
}