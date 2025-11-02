<?php
namespace PHPizza;
class SpecialPageUserLogout extends SpecialPage {
    public function __construct() {
        global $sitename;
        parent::__construct("UserLogout", "User Logout", <<<HTML
<h1>Logged Out</h1>
<p>
    You have been logged out of {$sitename}.
    If you have not been redirected, <a href="index.php">click here to return to the homepage</a>.
</p>
HTML);
    }
    public function getContent(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        // To log out the user, switch the account to the Guest user
        $userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        global $guestUsername, $guestPasswordB64;
        if ($userdb->verify_user_credentials($guestUsername, base64_decode($guestPasswordB64))){
            $user = $userdb->get_user_by_username($guestUsername);
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
            }
        }

        // Redirect to homepage after logout
        header("Location: index.php");
        exit();
    }
}