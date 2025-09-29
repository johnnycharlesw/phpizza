<?php
namespace PHPizza;
use PHPizza\PageRenderer;

class ErrorScreen
{
    private string $message;
    private PageRenderer $renderer;

    public function __construct(string $message = "Error") {
        $this->message = $message;
        $this->renderer = new PageRenderer();
    }

    /**
     * Build the HTML for an error page.
     * @param string $sitename
     * @param string|null $message
     * @param string $siteLanguage
     * @return string
     */
    private function _render(string $sitename, ?string $message = null, string $siteLanguage = 'en'): string
    {
        if ($message === null) {
            $message = $this->message;
        }

        $htmlContent = <<<HTML
<h1>PHPizza internal error</h1>
<p>
    {$message}
</p>
HTML;

        $pageTitle = 'PHPizza internal error';
        $description = 'An error has occurred in the PHPizza codebase and therefore this website cannot load.';
        $keywords = [];

        return $this->renderer->get_html_page(
            $sitename,
            $pageTitle,
            $description,
            $keywords,
            $htmlContent,
            $siteLanguage
        );
    }

    /**
     * Render the error page to the client. This method echoes the page and
     * optionally terminates execution.
     * @param string $sitename
     * @param string|null $message
     * @param bool $terminate
     * @return void
     */
    public function render(string $sitename, ?string $message = null, bool $terminate = true): void
    {
        http_response_code(500);
        if ($message !== null) {
            error_log($message);
        } else {
            error_log($this->message);
        }
        echo $this->_render($sitename, $message);
        if ($terminate) {
            exit(1);
        }
    }
}
