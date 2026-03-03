<?php
namespace PHPizza\PageManagement;

use DateTime;
use PHPizza\PageManagement\ImportFromCMSX;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\UserGroupDatabase;
use PHPizza\Exception;
use PHPizza\PageManagement\Page;
use PHPizza\PageManagement\PageDatabase;
use PHPizza\Database\Database;

class ImportFromWordPress implements ImportFromCMSX {
    public $sourcePath;
    private UserDatabase $userdb;
    private UserGroupDatabase $groupdb;
    private PageDatabase $pagedb;
    private array $cafelogConfig;
    private Database $cafelogDb;
    private $smileMap;

    public function __construct(string $sourcePath) {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->sourcePath = $sourcePath;
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->groupdb = new UserGroupDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->pagedb = new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->smileMap = [
            # Map b2smile filenames to emojis
            'icon_arrow.gif' => '➡️',
            'icon_biggrin.gif' => '😄',
            'icon_confused.gif' => '🤔',
            'icon_cool.gif' => '😎',
            'icon_cry.gif' => '😢',
            'icon_eek.gif' => '😢',
            'icon_evil.gif' => '👿',
            'icon_exclaim.gif' => '❗️',
            'icon_idea.gif' => '💡',
            'icon_lol.gif' => '😂',
            'icon_mad.gif' => '😡',
            'icon_mrgreen.gif' => '👽',
            'icon_neutral.gif' => '😐',
            'icon_question.gif' => '🤔',
            'icon_razz.gif' => '😂',
            'icon_redface.gif' => '😡',
            'icon_rolleyes.gif' => '😂',
            'icon_sad.gif' => '😢',
            'icon_smile.gif' => '😄',
            'icon_surprised.gif' => '🙀',
            'icon_twisted.gif' => '👿',
            'icon_wink.gif' => '😉',
        ];
        $this->readConfig();
    }

    /**
     * @disregard P1010 Undefined function
     */
    public function readConfig(){
        // Load the WordPress config
        $config = [];
        $configPhp = "";
        // This function is used to sandbox the WordPress config to prevent global leaks and parse it through.
        $path = $this->sourcePath . '/wp-config.php';
        if (file_exists($path)) {
            $configPhp = file_get_contents($path);
            $configPhp = preg_replace('/(?:require_once|require|include_once|include) ABSPATH \. [\'"]wp-settings.php[\'"]/', '# phpizza-no-wp-conflict', $configPhp);
            eval($configPhp);
        }
        
        if (!defined('WPINC')) {
            define('WPINC', 'wp-includes');
        }
        define('ABSPATH', $this->sourcePath);

        include $this->sourcePath . WPINC . '/version.php';
        include $this->sourcePath . WPINC . '/load.php';


        // Emulate what wp-settings.php would do
        /**
         * @disregard P1010 Undefined function
         */
        wp_initial_constants();

        /**
         * @disregard P1011 Undefined constant
         */
    	$dbusername = defined( 'DB_USER' ) ? DB_USER : '';
        /**
         * @disregard P1011 Undefined constant
         */
    	$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
        /**
         * @disregard P1011 Undefined constant
         */
    	$dbname = defined( 'DB_NAME' ) ? DB_NAME : '';
        /**
         * @disregard P1011 Undefined constant
         */
    	$dbhost = defined( 'DB_HOST' ) ? DB_HOST : '';

        # Slight divergence: Only operate if maintenance mode is on
        /** @disregard P1010 Undefined function */
        if (!wp_is_maintenance_mode()) {
            throw new Exception("Before importing the site into PHPizza, enable maintenance mode to ensure database consistency.");
        }

        // Collate the config

        $fileupload_realpath = $this->sourcePath . '/wp-content/uploads';
        $config = [
            'cafelog_version' => '0.6.2',
            'dbServer' => $dbhost,
            'dbUser' => $dbusername,
            'dbPassword' => $dbpassword,
            'dbName' => $dbname,
            'dbType' => 'mariadb',
            'fileUploadRealPath' => $fileupload_realpath,
            'fileUploadAllowedExtensions' => explode(" ",trim($fileupload_allowedtypes)),
            'useBBCode' => $use_bbcode,
            'useGreyMatterMarkup' => $use_gmcode,
            'useBalanceTags' => $use_balancetags,
            'useSmartQuotes' => $use_smartquotes,
            'telemetryEnabled' => $use_cafelogping ?? false || $use_weblogsping ?? false || $use_blodotgsping ?? false,
            'b2smiletrans' => (array)$b2smiletrans,
            'tablePosts' => (string)$tableposts,
            'tableCategories' => (string)$tablecategories,
            'tableComments' => (string)$tablecomments,
            'tableSettings' => (string)$tablesettings,
            'tableUsers' => (string)$tableusers,
        ];
        $this->cafelogConfig = $config;
        $this->cafelogDb = new Database($config['dbServer'], $config['dbUser'], $config['dbPassword'], $config['dbName'], $config['dbType']);
    }
    public function importPage(int $pageId): void {
        $tablePosts=$this->cafelogConfig['tablePosts'];
        $dbPage=$this->cafelogDb->fetchRow("SELECT * FROM ? WHERE ID=?", [$tablePosts, $pageId], 'si');
        $this->pagedb->createPage(
            date_format(
                new DateTime($dbPage['post_date']), 
                "archives/YYYY/MM/DD/{$dbPage['post_title']}"
            ), 
            $this->importPageContent(
                $dbPage['post_content']
            )
        );
    }

    public function importPageContent(string $pageMarkup): string {
        $pageMarkdown = $pageMarkup;
        // COnvert b2markup to Markdown

        foreach ($this->cafelogConfig['b2smiletrans'] as $emoticon => $filename) {
            // Convert each b2smile to an emoji
            try {
                $finalEmoji = $this->smileMap[$filename];
            } catch (\Throwable $th) {
                $finalEmoji = $emoticon;
            }
            $pageMarkdown=preg_replace("/\b$emoticon\b/", $finalEmoji, $pageMarkdown);
        }

        // If BBCode is used, convert it to Markdown formatting
        if ($this->cafelogConfig['useBBCode']) {
            $pageMarkdown = $this->convertBBCodeToMarkdown($pageMarkdown);
               
        }

        // If GreyMatter is used, convert it to Markdown formatting
        if ($this->cafelogConfig['useGreyMatterMarkup']) {
            $pageMarkdown = $this->convertGreyMatterMarkupToMarkdown($pageMarkdown);
        }

        // If balance tags is enabled and there are unclosed tags, automatically close them
        if ($this->cafelogConfig['useBalanceTags']) {
            $pageMarkdown = $this->balanceTags($pageMarkdown);
        }

        return $pageMarkdown;

    }

    public function balanceTags($content, $htmlTags = [
        "div" => "</div>",
        'p' => '</p>',
        'b' => '</b>',
        'i' => '</i>',
        'u' => '</u>',
        'strong' => '</strong>',
        'em' => '</em>',
    ]) {
        foreach ($htmlTags as $openTag => $closeTag) {
            $openTagPattern = '/' . preg_quote('<' . $openTag . '>', '/') . '/';
            $closeTagPattern = '/' . preg_quote($closeTag, '/') . '/';
            
            $openCount = preg_match_all($openTagPattern, $content);
            $closeCount = preg_match_all($closeTagPattern, $content);
            
            // If there are more open tags than close tags
            if ($openCount > $closeCount) {
                for ($i = 0; $i < ($openCount - $closeCount); $i++) {
                    $content .= $closeTag; // Append closing tags
                }
            }
        }
        
        return $content;
    }


    public function convertBBCodeToMarkdown($content, $patterns = [
        '/\[b\](.*?)\[\/b\]/s' => '**$1**',            // Bold
        '/\[i\](.*?)\[\/i\]/s' => '*$1*',               // Italics
        '/\[u\](.*?)\[\/u\]/s' => '++$1++',              // Underline (custom)
        '/\[url=(.*?)\](.*?)\[\/url\]/s' => '[$2]($1)', // URL
        '/\[img\](.*?)\[\/img\]/s' => '![]($1)',         // Image
        '/\[quote\](.*?)\[\/quote\]/s' => '> $1',       // Blockquote
        '/\[list\](.*?)\[\/list\]/s' => '$1',           // Lists (handled later)
        // Add more patterns as needed
    ]) {
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        return $content;
    }

    public function convertGreyMatterMarkupToMarkdown($content, $patterns = [
        '/\*\*(.*?)\*\*/s' => '**$1**',              // Bold
        '/\*(.*?)\*/s' => '*$1*',                    // Italics
        '/\#\#\s(.*?)\n/s' => '## $1' . "\n",        // H2 Header
        '/\#\s(.*?)\n/s' => '# $1' . "\n",           // H1 Header
        '/~~(.*?)~~/s' => '~~$1~~',                  // Strikethrough
        '/\[(.*?)\]\((.*?)\)/s' => '[$1]($2)',        // Inline Links
        '/!\[(.*?)\]\((.*?)\)/s' => '![]($2)',       // Images
    ]) {
        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        return $content;
    }

    public function importPageMeta(int $pageId): void {
        // Implement the logic to import the metadata of a page from a Cafelog source
        // Example: Read the page metadata from a file or database
        // Then, call the parent method to import the page metadata
    }
    public function importAllPages(): void {
        $tablePosts = $this->cafelogConfig['tablePosts'];
        $dbPages = $this->cafelogDb->fetchAll("SELECT * FROM ?", [$tablePosts], 's');
        foreach ($dbPages as $dbPage) {
            $this->importPage($dbPage['ID']);
        }
    }
    public function importComments(int $pageId): void {
        // Implement the logic to import comments for a page from a Cafelog source
        // Example: Read the comments from a file or database
        // Then, call the parent method to import comments
    }
    public function importPageTags(int $pageId): void {
        // Implement the logic to import tags for a page from a Cafelog source
        // Example: Read the tags from a file or database
        // Then, call the parent method to import tags
    }
    public function importUsers(): void {
        // Implement the logic to import users for a page from a Cafelog source
        // Example: Read the user data from a file or database
        // Then, call the parent method to import users
    }
    public function importGroups(): void {
        // Cafelog did not have a fully-fleshed group system, so we will create equivalent groups and assign users them based on value

        $this->groupdb->create_user_group("commenter"); // Access Level 0 in Cafelog
        $this->groupdb->create_user_group("contributor"); // Access Level 1 in Cafelog
        $this->groupdb->create_user_group("author"); // Access Level 2 in Cafelog
        $this->groupdb->create_user_group("editor"); // Access Level 5 in Cafelog
        // There is already an "admin" group in PHPizza

        // Assign equivalent permissions

        // Commenters can only comment
        $this->groupdb->grant_permission_to_user_group("commenter", "comment");

        // Contributors and authors can edit as well as comment
        foreach (["contributor", "author"] as $groupname) {
            $this->groupdb->grant_permission_to_user_group($groupname, "edit");
            $this->groupdb->grant_permission_to_user_group($groupname, "comment");
        }
        // Editors can edit, comment, and manage pages
        foreach (["editor"] as $groupname) {
            $this->groupdb->grant_permission_to_user_group($groupname, "edit");
            $this->groupdb->grant_permission_to_user_group($groupname, "comment");
            $this->groupdb->grant_permission_to_user_group($groupname, "manage");
        }
    }
    public function importAttachments(int $pageId): void {
        // Implement the logic to import attachments for a page from a Cafelog source
        // Example: Read the attachment data from a file or database
        // Then, call the parent method to import attachments

        foreach (scandir($this->cafelogConfig['fileUploadRealPath']) as $year) {
            foreach (scandir($year) as $month) {
                $this->_importAttachments($month);
            }
        }
    }

    public function _importAttachments(string $path): void {
        // Implement the logic to import attachments for a page from a WordPress source
        // Example: Read the attachment data from a file or database
        // Then, call the parent method to import attachments

        foreach (scandir($path) as $file) {
            if (!file_exists('/uploads/'.basename($file))) {
                # In that case, we do need to bring it over from WordPress, because it was not uploaded to PHPizza.
                rename($file, '/uploads/'.basename($file));
            }
        }
    }
    public function importEverything(): void {
        // Implement the logic to import all pages, content, metadata, comments, tags, users, groups, and attachments from a Cafelog source
        // Example: Read all data from a file or database
        
        // Users and groups
        $this->importGroups();
        $this->importUsers();

        // Pages
        $this->importAllPages();
    }
    public function getSourcePath(): string {
        return $this->sourcePath;
    }

    public function __invoke()
    {
        return $this->importEverything();
    }
}