<?php
namespace PHPizza\HTTPHandling;

abstract class HTTPEndpointHandler {
    private int $statusCode;
    private string $body;
    private $statusMap;
    public function __construct() {
        $this->statusCode=200;
        $this->statusMap=new StatusMap();
        $this->body = <<<html
        <h1>It works!</h1>
        html;
    }

    public function setStatusCode($code){
        $this->statusCode=$code;
    }

    public function getStatusCode(){
        return $this->statusCode;
    }

    public function setBody($body){
        $this->body=$body;
    }

    public function getBody(){
        return $this->body;
    }

    public function send_response_to_client(){
        $this->statusMap->send($this->statusCode,$this->body);
    }
}