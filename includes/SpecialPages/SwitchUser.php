<?php
namespace PHPizza\SpecialPages;

use Override;
use PHP_CodeSniffer\Generators\HTML;
use PHPizza\IllegalStateException;
use PHPizza\Rendering\Pizzadown;
use PHPizza\UserManagement\UserDatabase;
class SwitchUser extends SpecialPage {
    private UserDatabase $userdb;
    public function __construct($name) {
        global $sitename;
        parent::__construct("SwitchUser", "User Login", "");
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
    }

    
    public function getContent()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        global $sitename, $siteLogoPath;
        $output = '';
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $loginId = $_POST["loginid"];
            $logins = $_SESSION['logins'];
            # Check if login ID is valid
            if ($loginId < count($logins)) {
                # Check if login is valid
                $login = $logins[$loginId];
                if ($login['username'] && $login['user_id']>-1) {
                    # Switch to that login
                    $_SESSION['user_id'] = $login['user_id'];
                    $_SESSION['username'] = $login['username'];
                    header("Location: /");
                    exit();
                } else {
                    # Login state invalid - assume compromise, log user out IMMEDIATELY!
                    $_SESSION['logins'] = [];
                    global $guestUsername, $guestPasswordB64;
                    if ($this->userdb->verify_user_credentials($guestUsername, base64_decode($guestPasswordB64))){
                        $user = $this->userdb->get_user_by_username($guestUsername);
                        if ($user) {
                            $_SESSION['user_id'] = $user->getId();
                            $_SESSION['username'] = $user->getUsername();
                        }
                    }

                }
            } else {
                throw new IllegalStateException("The specified login ID was not found.");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
            $output = <<<HTML
            <h1>Switch User</h1>
            HTML;
            $logins = $_SESSION['logins'];
            foreach ($logins as $login) {
                $output .= <<<HTML
                <form method="post" action="index.php?title=SwitchUser.php">
                    <!-- <img src="/node_modules/feather-icons/dist/icons/user.svg" class="icon" width="128" height="128"></img>
                    <br> -->
                    <input type="text" name="loginid" value="{$login['loginid']}" hidden/>
                    <button>Log in as {$login['username']}</button>
                </form>
                HTML;
            }
            $output .= <<<HTML
            <br>
            <a href="/UserLogin.php" class="btn">Log in as another user</a>
            <a href="/UserLogout.php" class="btn">Log out all users</a>
            HTML;
            return $output;
        }
    }
}