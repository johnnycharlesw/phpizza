<?php
namespace PHPizza;
class SpecialPageEditor extends SpecialPage {
    private PageDatabase $pagedb;
    private UserDatabase $userdb;
    private Pizzadown $pd;
    public string $pagetitle;

    public function __construct() {
        global $homepageName, $dbServer, $dbUser, $dbName, $dbPassword, $dbType;
        $this->pagedb=new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->userdb=new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $editingPageTitle=$_GET["page_to_edit"] ?? $_POST['page_to_edit'] ?? $homepageName;
        parent::__construct("Editor","Editing $editingPageTitle","");
        $this->pagetitle=$editingPageTitle;
    }

    public function getContent()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user=$this->userdb->get_user_by_username($_SESSION["username"]);
        if ($user === null) {
            throw new \Exception("The active user context is corrupted", 1);
        }
        if (!$user->can_I_do("edit")) {
            return "You do not have permission to edit this page.";
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $content=$_POST["content_pd"];
            $this->pagedb->updatePage($this->pagetitle, $content);
            http_response_code(302);
            header("Location: /{$this->pagetitle}");
            return "";
        } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
            
        // GET
        $this->pd = new Pizzadown();
        $page = $this->pagedb->getPage($this->pagetitle);

        // Escape content so it shows as literal source in the textarea
        $raw = (string)($page["content"] ?? '');
        // ENT_NOQUOTES so quotes remain editable; also guard against closing the textarea
        $escaped = htmlspecialchars($raw, ENT_NOQUOTES, 'UTF-8');
        $escaped = str_replace('</textarea>', '&lt;/textarea&gt;', $escaped);

        // Return plain HTML (not Markdown) so BrowserEntryPoint doesn't render the header as Markdown
        return <<<HTML
<h1>Editing page {$this->pagetitle}</h1>
<form method="post" action="/index.php?title=PHPizza:Editor">
  <input type="text" id="page_to_edit" name="page_to_edit" value="{$this->pagetitle}" hidden>
  <textarea id="content_pd" name="content_pd" rows="20" cols="80">{$escaped}</textarea>
  <input type="submit" value="Save">
</form>
HTML;
        }
    }
}