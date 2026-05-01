<?php
namespace PHPizza\COPPACompliance;

use DateTime;
use PHPizza\UserManagement\User;
use PHPizza\UserManagement\UserDatabase;

class AB1043ComplianceShim {
    const YES = true;
    const NO = false;

    private $userdb;

    public function __construct()
    {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    public function _doAgeVerification(User $minor): int {
        if (!empty($verifyAge)) {
            $ageDatetime = $minor->when_was_I_born();
            $ageGroup = date_format($ageDatetime, "YY") - date_format(new DateTime('now'), "YY") < -18;
            return $ageGroup * 18;
        }
        return 18;
    }

    public function ageVerify(string $minor_username): int {
        $user = $this->userdb->get_user_by_username($minor_username);
        return $this->_doAgeVerification($user);
    }
}