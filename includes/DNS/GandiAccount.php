<?php
namespace PHPizza\DNS;
use PHPizza\UserManagement\User;

class GandiAccount {
    public User $owner;

    public string $country;
    public string $streetaddr;
    public string $type;
}