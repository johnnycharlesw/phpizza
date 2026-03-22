<?php
namespace PHPizza\PageManagement;

use DateTime;
use DOMDocument;
use DOMNode;
use PHPizza\PageManagement\ImportFromCMSX;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\UserGroupDatabase;
use PHPizza\Exception;
use PHPizza\PageManagement\Page;
use PHPizza\PageManagement\PageDatabase;
use PHPizza\Database\Database;
use League\HTMLToMarkdown\Converter\DefaultConverter;
use League\HTMLToMarkdown\Element;

class ImportFromWordPress implements ImportFromCMSX {
    public $sourcePath;
    private UserDatabase $userdb;
    private UserGroupDatabase $groupdb;
    private PageDatabase $pagedb;
    private array $wp_config;
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
            'cafelog_version' => $wp_version,
            'dbServer' => $dbhost,
            'dbUser' => $dbusername,
            'dbPassword' => $dbpassword,
            'dbName' => $dbname,
            'dbType' => 'mariadb',
            'fileUploadRealPath' => $fileupload_realpath,
            'fileUploadAllowedExtensions' => explode(" ",trim($fileupload_allowedtypes)),
            'useBalanceTags' => $use_balancetags,
            'useSmartQuotes' => $use_smartquotes,
            'telemetryEnabled' => false,
            'b2smiletrans' => (array)$b2smiletrans,
            'tablePosts' => $table_prefix . "posts",
            'tableCategories' => $table_prefix . "terms",
            'tableComments' => $table_prefix . "comments",
            'tableSettings' => $table_prefix . "options",
            'tableUsers' => $table_prefix . "users",
        ];
        $this->wp_config = $config;
        $this->cafelogDb = new Database($config['dbServer'], $config['dbUser'], $config['dbPassword'], $config['dbName'], $config['dbType']);
    }
    public function importPage(int $pageId): void {
        # Retrieve post/page from database
        $tablePosts=$this->wp_config['tablePosts'];
        $dbPage=$this->cafelogDb->fetchRow("SELECT * FROM ? WHERE ID=?", [$tablePosts, $pageId], 'si');

        # Is it a post or a page?
        $blogPageFormat = "archives/YYYY/MM/DD/{$dbPage['post_title']}";
        $pageFormat = $dbPage['post_title'];
        $urlFormat = '';
        if ($dbPage['post_type'] == 'post') {
            $urlFormat = $blogPageFormat;
        } elseif ($dbPage['post_type'] == 'page' || $dbPage['post_type'] == 'revision') {
            $urlFormat = $pageFormat;
        }

        # Don't import drafts!
        if ($dbPage['post_status'] == "draft" || $dbPage['post_status'] == "auto-draft") {
            return;
        }

        # Import the page
        $this->importAttachments($pageId);
        $this->pagedb->createPage(
            date_format(
                new DateTime($dbPage['post_date']), 
                $urlFormat
            ), 
            $this->importPageContent(
                $dbPage['post_content']
            )
        );
    }

    public function importPageContent(string $pageMarkup): string {
        $pageMarkdown = $pageMarkup;
        // COnvert wp_markup to Markdown

        foreach ($this->wp_config['b2smiletrans'] as $emoticon => $filename) {
            // Convert each b2smile to an emoji
            try {
                $finalEmoji = $this->smileMap[$filename];
            } catch (\Throwable $th) {
                $finalEmoji = $emoticon;
            }
            $pageMarkdown=preg_replace("/\b$emoticon\b/", $finalEmoji, $pageMarkdown);
        }

        // If balance tags is enabled and there are unclosed tags, automatically close them
        if ($this->wp_config['useBalanceTags']) {
            $pageMarkdown = $this->balanceTags($pageMarkdown);
        }

        // Strip wordpress-specific stuff

        $pageMarkdown = preg_replace('/<!-- wp.+ (?:/)-->/', '', $pageMarkdown);

        $htmlToMarkdown = new DefaultConverter();

        $domNode = new DOMDocument();
        $domNode->loadHTML($pageMarkdown);
        $pageMarkdown = $htmlToMarkdown->convert(new Element($domNode));

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

    public function importPageMeta(int $pageId): void {
        // Implement the logic to import the metadata of a page from a Cafelog source
        // Example: Read the page metadata from a file or database
        // Then, call the parent method to import the page metadata
    }
    public function importAllPages(): void {
        $tablePosts = $this->wp_config['tablePosts'];
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
        $tableusers = $this->wp_config['tableUsers'];
        $wpUsers = $this->cafelogDb->fetchAll("SELECT * FROM $tableusers");
        foreach ($wpUsers as $wpUser) {
            # Collate info from schema
            require ABSPATH . WPINC . '/capabilities.php';
            require ABSPATH . WPINC . '/class-wp-roles.php';
            require ABSPATH . WPINC . '/class-wp-role.php';
            require ABSPATH . WPINC . '/class-wp-user.php';
            require ABSPATH . WPINC . '/user.php';

            /** @disregard */
            $wpuser = new WP_USER( $wpUser['ID'] );
            $groups = [];
            if ( !empty( $wpuser->roles ) && is_array( $wpuser->roles ) ) {
                foreach ( $wpuser->roles as $role ) {
                    $groups[] = $role;
                }
            }

            $username = $wpuser->user_login;
            $password = $wpuser->user_pass;
            $email = $wpuser->user_email;

            # Import account
            $importedUser = $this->userdb->create_user($username, $password);
            $importedUser->hey_I_got_a_new_email($email);
            $this->userdb->update_user_password_hash($importedUser->id, $password);
            foreach ($groups as $group) {
                $this->groupdb->add_user_to_group($importedUser->id, $this->groupdb->get_user_group_by_name($group)->id);
            }
            
        }
    }
    public function importGroups(): void {
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

        foreach (scandir($this->wp_config['fileUploadRealPath']) as $year) {
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