<?php
namespace PHPizza\SpecialPages;
use PHPizza\ArtificialIntelligence\Ollama;
use PHPizza\Rendering\Pizzadown;

class MistralBackedAgentRenderer extends SpecialPage {
    private $ollama;
    private $pizzadown;
    private $template ;
    public function __construct($instanceUrl = "http://localhost:11434") {
        global $specialPrefix;
        $input = $_GET['input'] ?? $_POST['input'] ?? 'It appears no input was provided actually, nevermind, Mistral.';
        parent::__construct("MistralBackedAgentRenderer", "Mistral Backed Agent Renderer", ""); // Content will be generated in getContent()
        $this->template = <<<PIZZADOWN
<style>
    header, footer, aside.sidebar {
        display:none;
    }
    article {
        margin: auto;
        max-width: 800px;
    }
</style>
<script src="/js.php?f=phpizza-cms-js/mistral-backed-agent-ui.js"></script>
<div class="phpizza-cms-mistral-backed-agent-ui">
    <div class="context">
    {{{context}}}
    </div>
    <form method="POST" action="index.php?title={$specialPrefix}MistralBackedAgentRenderer">
        <label for="user_input">Enter your input for the Mistral-backed agent:</label><br>
        <input type="text" id="context" name="context" hidden value=""><br><br>
        <textarea id="user_input" name="user_input" rows="4" cols="50" required></textarea><br><br>
        <input type="submit" value="Submit">
    </form>
</div>
PIZZADOWN;
        $this->ollama = new Ollama($instanceUrl, $input);
        $this->pizzadown = new Pizzadown(false);
    }

    public function getContent(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        global $sitename;
        $output = '';
        $args = [
            "sitename" => $sitename,
            "context" => "",
            "context_md" => "", # Internally used to build context before templating
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # Handle form submission
            $context = $_POST['context'] ?? [];
            $user_input = $_POST['user_input'] ?? '';
            $agent_response = $this->ollama->getResponseText($user_input);
            $args['context_md'] .= "\n\n<span class=\"user-input\">**User Input:** " . htmlspecialchars($user_input) . "</span>\n\n";
            $args['context_md'] .= "\n\n<span class=\"agent-reply\">**Agent Response:** " . $agent_response . "</span>\n\n";
        }
        $args['context'] = $this->pizzadown->templateText($args['context_md'] ?? '', $args);
        $output = $this->pizzadown->templateText($this->template, $args);
        return $output;
    }
}