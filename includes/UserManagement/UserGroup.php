<?php
namespace PHPizza\UserManagement;
class UserGroup {
    // This is UserGroup, not UserGroupDatabase.
    public $id;
    public $name;
    public $members;
    public $permissions;

    public function __construct(int $id, string $name, array $members, array $permissions) {
        $this->id = $id;
        $this->name = $name;
        $this->members=$members;
        $this->permissions=$permissions;
    }

    public function am_I_in_this(User $user){
        return in_array($user->id, $this->members);
    }

    public function can_this_group_do(string $action){
        return in_array($action, $this->permissions);
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

}