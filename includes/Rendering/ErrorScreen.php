<?php
namespace PHPizza\Rendering;

use Throwable;

class ErrorScreen
{
    private string $message;
    private PageRenderer $renderer;

    public function __construct(string $message = "Error") {
        $this->message = $message;
        $this->renderer = new PageRenderer(["isErrorScreen" => true]);
    }

    /**
     * Build the HTML for an error page.
     * @param string $sitename
     * @param string|null $message
     * @param string $siteLanguage
     * @return string
     */
    private function _render(string $sitename, ?Throwable $throwable = null, string $siteLanguage = 'en'): string
    {
        $message = $throwable->getMessage();
        if ($message === null) {
            $message = $this->message;
        }
        $trace = "";
        $traceAsArray = $throwable->getTrace();
        foreach ($traceAsArray as $trace_) {
            $functionArgs = var_export($trace_["args"]);
            $file = $trace_["file"];
            $function = $trace_["function"];
            $line = $trace_["line"];
            $trace .= <<<HTML
            in function {$function} in {$file} on line {$line}, with these arguments:<br>
            <pre><code class="language-php">
            {$functionArgs}
            </code></pre>
            <br>
            HTML;
        }
        $htmlContent = <<<HTML
<h1>PHPizza internal error</h1>
<p>
    It looks like we have been having some technical difficulties on our end.<br>
    Please try again later. If this problem persists, please contact {$sitename} support.
    <br>
    Exception: {$message}<br>
    {$trace}
</p>
HTML;

        $pageTitle = 'PHPizza internal error';
        $description = "An error has occurred in the PHPizza codebase and therefore $sitename cannot load.";
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
    public function render(string $sitename, ?Throwable $throwable = null, bool $terminate = true): void
    {
        // http_response_code(500);
        $message = $throwable->getMessage();
        if ($message !== null) {
            error_log($message);
        } else {
            error_log($this->message);
        }
        echo $this->_render($sitename, $throwable);
        if ($terminate) {
            exit(1);
        }
    }
}
