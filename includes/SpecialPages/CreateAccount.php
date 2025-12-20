<?php
namespace PHPizza\SpecialPages;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\User;
use PHPizza\Rendering\Pizzadown;
class CreateAccount extends SpecialPage {
    public function __construct() {
        global $sitename;
        parent::__construct("CreateAccount", "Create Account", ""); // Content will be generated in getContent()
    }
    public function getContent(){
        global $specialPrefix;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        global $sitename, $siteLogoPath;
        $output = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
            # Handle account creation form submission
            $username = $_POST['username'] ?? '';
            $password_b64 = $_POST['password_b64'] ?? '';
            $password = $_POST['password'] ?? base64_decode($password_b64);
            $confirm_password_b64 = $_POST['confirm_password_b64'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? base64_decode($confirm_password_b64);
            if ($password !== $confirm_password) {
                $output = "<div class=\"create-account-error\">Passwords do not match.</div>";
            } else {
                # Create new user in the database
                $userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
                if ($userdb->get_user_by_username($username)) {
                    $output = "<div class=\"create-account-error\">Username already exists.</div>";
                } else {
                    if ($userdb->create_user($username, $password)) {
                        // Redirect to login page after successful account creation
                        header("Location: index.php?title={$specialPrefix}UserLogin");
                        exit();
                    } else {
                        $output = "<div class=\"create-account-error\">Failed to create account. Please try again.</div>";
                    }
                }
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
# ![{{{sitename}}} Logo]({{{siteLogoPath}}}) Create an Account on {{{sitename}}}
<form method="POST" action="index.php?title=PHPizza:CreateAccount">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>
    <label for="confirm_password">Confirm Password:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>
    <input type="submit" value="Create Account">
</form>
Already have an account? [Log in here.](/index.php?title=PHPizza:UserLogin)
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