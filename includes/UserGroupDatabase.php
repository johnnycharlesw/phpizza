<?php
namespace PHPizza;

class UserGroupDatabase {
    // Not using PDO, instead using the Database class
    private $db;
    public function __construct($dbServer, $dbUser, $dbPassword, $dbName, $dbType) {
        $this->db = new Database($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        if ($this->db->get_table_exists('user_groups') === false) {
            throw new Exception("The 'user_groups' table could not be found. Please update the database using the schema file.", 1);
            
        }
    }
    public function get_user_group_by_name(string $name): ?UserGroup {
        $row = $this->db->fetchRow("SELECT * FROM user_groups WHERE name = ?", [$name]);
        if ($row && is_array($row) && isset($row['id'])) {
            return new UserGroup((int)$row['id'], $row['name'], str_getcsv($row['members']), str_getcsv($row['permissions']));
        }
        return null;
    }
    public function get_user_group_by_id(int $id): ?UserGroup {
        $row = $this->db->fetchRow("SELECT * FROM user_groups WHERE id = ?", [$id]);
        if ($row && is_array($row) && isset($row['id'])) {
            return new UserGroup((int)$row['id'], $row['name'], str_getcsv($row['members']), str_getcsv($row['permissions']));
        }
        return null;

    }

    // User groups do not have shared passwords
    public function create_user_group(string $name): ?UserGroup {
        $rows = $this->db->execute("INSERT INTO user_groups (name, members, permissions) VALUES (?)", [$name, "", []]);
        if ($rows === false || $rows === 0) {
            return null;
        }
        $newGroupId = $this->db->getLastInsertId();
        return new UserGroup((int)$newGroupId, $name, [], []);
    }
    public function update_user_group_name(int $id, string $newName): bool {
        $rowsAffected = $this->db->execute("UPDATE user_groups SET name = ? WHERE id = ?", [$newName, $id]);
        return $rowsAffected > 0;
    }

    // Actually, you can delete user groups, but not accounts themselves because the database is relationally linked to COPPA consents
    public function delete_user_group(int $id): bool {
        $rowsAffected = $this->db->execute("DELETE FROM user_groups WHERE id = ?", [$id]);
        return $rowsAffected > 0;
    }

    // the user_groups table has a "members" column that stores a CSV  of userids
    public function add_user_to_group(int $userId, int $groupId): bool {
        $query = "UPDATE user_groups SET members = CONCAT(members, ?, ',') WHERE id = ?";
        return $this->db->execute($query, [$userId, $groupId]);
    }

    public function _get_all_user_groups(): ?array {
        $query = "SELECT * FROM user_groups";
        return $this->db->fetchAll($query);
    }

    public function get_all_user_groups(): ?array{
        $groups_ = $this->_get_all_user_groups();
        $groups = [];
        foreach ($groups_ as $dbRow) {
            $groups[] = new UserGroup((int)$dbRow['id'], $dbRow['name'], str_getcsv($dbRow['members']), str_getcsv($dbRow['permissions']));
        }
        return $groups;
    }

    // the user_group_members table does not exist in the schema.
    public function get_user_groups_by_user_id(int $userId): array {
        # Loop over all groups and check if user is a member
        global $dbServer,$dbUser,$dbPassword,$dbName,$dbType;
        $userdb=(new UserDatabase($dbServer,$dbUser,$dbPassword,$dbName,$dbType));
        $groups = $this->get_all_user_groups();
        $userGroups = [];
        foreach ($groups as $group) {
            if ($group->am_I_in_this($userdb->get_user_by_id($userId))) {
                $userGroups[] = $group;
            }
        }
        return $groups;
    }
}