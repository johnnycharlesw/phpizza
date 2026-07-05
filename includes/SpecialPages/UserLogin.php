<?php
namespace PHPizza\SpecialPages;
use PHPizza\Rendering\Pizzadown;
use PHPizza\UserManagement\UserDatabase;

class UserLogin extends SpecialPage {
    public function __construct($name) {
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
                if ($user && (!$user->am_I_blocked())) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    if ($_SESSION['logins']) {
                        $_SESSION['logins'][] = [
                            'loginid' => count($_SESSION['logins']),
                            'user_id' => $user->getId(),
                            'username' => $user->getUsername()
                        ];
                    } else {
                        $_SESSION['logins'] = [
                            [
                                'loginid' => 0,
                                'user_id' => $user->getId(),
                                'username' => $user->getUsername()
                            ]
                        ];
                    }
                    // Redirect to homepage or another page after successful login
                    if ($_GET['returnto']) {
                        header("Location: ".$_GET['returnto']);
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                }
            } else {
                $output = "<div class=\"login-error\">Invalid username or password.</div>";
            }
        } else {
            $pizzadown = new Pizzadown(false);
            $output = $pizzadown->templateText(<<<MARKDOWN
<style>
    img.login-logo {
        width: 64px;
        height: 64px;
    }
</style>
<h1><img alt="{{{sitename}}} Logo" src="{{{siteLogoPath}}}" class="login-logo" /> Log in to {{{sitename}}}</h1>
<form method="POST" action="index.php?title=PHPizza:UserLogin">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Log In">
</form>
Do you have an account yet? <a href="/index.php?title=PHPizza:CreateAccount">If not, sign up here.</a>
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