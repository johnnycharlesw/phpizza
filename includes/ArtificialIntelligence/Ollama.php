<?php
namespace PHPizza;
class Ollama {
    private $instanceUrl;
    public $input;
    public function __construct($instanceUrl = "http://localhost:11434", $input) {
        $this->instanceUrl = rtrim($instanceUrl, '/');
        $this->input = $input;
    }

    public function generateResponse($input, $context = []) {
        // Send a POST request to the Ollama instance
        $ch = curl_init();
        #$model='mistral-small';
        $model="gemma3"; # Using Gemma 3 as a placeholder model to test if it was a model problem
        curl_setopt($ch, CURLOPT_URL, $this->instanceUrl . "/api/generate");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'prompt' => $input,
            'model' => $model, // THe regular Mistral model is used here because the agent will be interacted with via text and images
            'system' => <<<MARKDOWN
            You are the Mistral AI agent intergrated into [the PHPizza](https://github.com/johnnycharlesw/phpizza).
            Your purpose is to assist users in managing and creating content on their PHPizza-powered website.
            You were embedded into the page or template via:
            !MistralBackedAgent[{$this->input}]
            PHPizza is built on the LAMP stack (Linux, Apache, MySQL, PHP) and uses markdown for content formatting.
            MARKDOWN,
            'context' => $context,
            'options' => [
                'temperature' => 0.85, // Slightly higher temperature than default for more creative responses but still coherent
            ],
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getResponseText($input) {
        $response = $this->generateResponse($input);
        return $response['text'] ?? '';
    }

    

}