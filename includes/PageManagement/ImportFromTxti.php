<?php
namespace PHPizza\PageManagement;

/**
 * Farewell, [txti](https://txti.es).
 * You served the internet well back in 2014.
 * Now it's time for PHPizza in 2026.
 */
class ImportFromTxti implements ImportFromCMSX {
    public $sourcePath;
    public $css;
    private PageDatabase $pagedb;
    public $notImplementedMessage = 'Not implemented, nor was it implemented in Txti either 🤣';
    public function __construct(string $sourcePath)
    {
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->pagedb = new PageDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->sourcePath=$sourcePath;
        $css = file_get_contents($this->sourcePath . '/stylesheet.css');
        $this->css=$css;
    }

    public function getFlatFileDbPages(): array {
        $_pages = scandir($this->sourcePath);
        $pages=[];
        foreach ($_pages as $page) {
            if (str_ends_with($page, '.md')) {
                $pages[] = $page;
            }
        }
        return $pages;
    }

    public function getFlatFileDbAttachments(): array {
        $_attachments = scandir($this->sourcePath);
        $attachments=[];
        foreach ($_attachments as $attachment) {
            if (!str_ends_with($attachment, '.md')) {
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    public function importPageContent(string $pageMarkup)
    {
        return $pageMarkup; # Well, the "B" behind Txti also used Markdown, so no need.
    }

    public function importPage(int $pageId): void
    {
        $pages = $this->getFlatFileDbPages();
        $page = $pages[$pageId];
        $this->importAttachments($pageId);
        $this->pagedb->createPage("$page.html", file_get_contents($this->sourcePath . "/$page"));
    }

    public function importPageMeta(int $pageId)
    {
        throw new \Exception($this->notImplementedMessage);
    }

    public function importPageTags(int $pageId): void
    {
        throw new \Exception($this->notImplementedMessage);
    }

    public function importAttachments(int $pageId): void
    {
        $attachments = $this->getFlatFileDbAttachments();
        foreach ($attachments as $attachment) {
            rename($this->sourcePath . "/$attachment", "/uploads/$attachment");
        }
    }

    public function importUsers(): void
    {
        throw new \Exception($this->notImplementedMessage);
    }

    public function importComments(int $pageId): void
    {
        throw new \Exception($this->notImplementedMessage);
    }

    public function importGroups(): void
    {
        throw new \Exception($this->notImplementedMessage);
    }

    public function importAllPages(): void
    {
        $pages = $this->getFlatFileDbPages();
        for ($i=0; $i < count($pages); $i++) { 
            $this->importPage($i);
        }
    }

    public function importStyling() {
        return;
    }

    public function importEverything(): void
    {
        $this->importAllPages();
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }
}