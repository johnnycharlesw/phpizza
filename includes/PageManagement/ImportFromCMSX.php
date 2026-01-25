<?php
namespace PHPizza\PageManagement;
/**
 * Interface
 */
interface ImportFromCMSX {
    public function __construct(string $sourcePath);

    # Pages
    public function importPage(string $pageId): void;
    public function importPageContent(string $pageId): void;
    public function importPageMeta(string $pageId): void;
    public function importAllPages(): void;
    # Page-attached stuff
    public function importComments(string $pageId): void;
    public function importPageTags(string $pageId): void;

    # Users/RBAC
    public function importUsers(): void;
    public function importGroups(): void;

    # Attachments/Uploads
    public function importAttachments(string $pageId): void;
    
    public function importEverything(): void;
    public function getSourcePath(): string;
}