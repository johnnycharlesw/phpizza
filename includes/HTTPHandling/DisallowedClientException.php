<?php
namespace PHPizza\HTTPHandling;

use Override;
use PHPizza\Exception;
use Throwable;

class DisallowedClientException extends Exception {
    public function __construct(string|null $message, $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?? "Sorry, but it looks like you are using a client not allowed by PHPizza.";
        return parent::__construct($message, $code, $previous);
    }
}