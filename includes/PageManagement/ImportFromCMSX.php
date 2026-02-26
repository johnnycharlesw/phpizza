<?php
namespace PHPizza\PageManagement;
/**
 * Interface
 */
interface ImportFromCMSX {
    public function __construct(string $sourcePath);

    # Pages
    public function importPage(int $pageId): void;
    public function importPageContent(string $pageMarkup);
    public function importPageMeta(int $pageId);
    public function importAllPages(): void;
    # Page-attached stuff
    public function importComments(int $pageId): void;
    public function importPageTags(int $pageId): void;

    # Users/RBAC
    public function importUsers(): void;
    public function importGroups(): void;

    # Attachments/Uploads
    public function importAttachments(int $pageId): void;
    
    public function importEverything(): void;
    public function getSourcePath(): string;
}