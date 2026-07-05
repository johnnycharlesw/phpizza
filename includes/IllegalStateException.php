<?php
namespace PHPizza;

use Throwable;
use Override;

class IllegalStateException extends Exception {
    #[Override]
    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        return parent::__construct($message ?? "An invalid state has been detected", $code, $previous);
    }
}