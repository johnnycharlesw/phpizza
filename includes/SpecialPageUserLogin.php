<?php
namespace PHPizza;


class SpecialPageUserLogin extends SpecialPage {
    public function __construct() {
        global $sitename;
        parent::__construct("UserLogin", "User Login", "");
    }

    public function getContent(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        global $sitename, $siteLogoPath;
        $output = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
            # Handle login form submission
            $username = $_POST['username'] ?? '';
            $password_b64 = $_POST['password_b64'] ?? '';
            $password = $_POST['password'] ?? base64_decode($password_b64);
            # Check if username and password are valid
            $userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
            if ($userdb->verify_user_credentials($username, $password)) {
                $user = $userdb->get_user_by_username($username);
                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['is_admin'] = $user->isAdmin();
                    // Redirect to homepage or another page after successful login
                    header("Location: index.php");
                    exit();
                }
            } else {
                $output = "<div class=\"login-error\">Invalid username or password.</div>";
            }
        } else {
            $pizzadown = new Pizzadown(false);
            $output = $pizzadown->templateText(<<<MARKDOWN
<style>
    h1 > img {
        width: 64px;
        height: 64px;
    }
</style>
# ![{{{sitename}}} Logo]({{{siteLogoPath}}}) Log in to {{{sitename}}}
<form method="POST" action="index.php?title=PHPizza:UserLogin">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Log In">
</form>
MARKDOWN,
                [
                    "sitename" => $sitename,
                    "siteLogoPath" => $siteLogoPath,
                ]
            );
        }

        return $output;
    }
}