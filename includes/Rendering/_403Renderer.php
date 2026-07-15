<?php
namespace PHPizza\Rendering;
class _403Renderer {
  private string $forbiddenAction;
  public function __construct(string $forbiddenAction) {
    $this->forbiddenAction = $forbiddenAction;
  }

  public function render(){
         return <<<HTML
            <h1><img src="/node_modules/feather-icons/dist/icons/slash.svg" class="icon"></img> Access Denied</h1>
            <p>You do not have permission to {$this->forbiddenAction}.</p>
            HTML;  
  }
}