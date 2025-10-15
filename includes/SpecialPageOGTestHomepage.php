<?php
namespace PHPizza;

// The original test homepage from early alpha versions of PHPizza CMS, added as a special page both for testing special pages and for nostalgia.
// Now used as an easter egg.


class SpecialPageOGTestHomepage extends SpecialPage {
    public function __construct() {
        // OG as in Original, not Open Graph
        global $sitename;
        parent::__construct("OGTestHomepage", "OG Test Homepage", <<<HTML
<h1>Welcome to {$sitename}!</h1>
<p>
    This is the homepage of {$sitename}.
</p>
HTML);
    }
}