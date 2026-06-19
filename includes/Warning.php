<?php
namespace PHPizza;

use Override;
use Throwable;

class Warning extends \Exception {
    public function __construct($message, $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return "Warning: " . $this->getMessage();
    }
}