<?php
namespace PHPizza\HTTPHandling;

use Rowbot\URL\URL;

class GETParameterParser {
    public string $url;
    public function __construct(?string $url) {
        $this->url = $_SERVER['REQUEST_URI'];
    }

    public function _parse_get_params(){
        $url = new URL($this->url);
        $searchParams = $url->searchParams;
        foreach ($searchParams->list as $key => $value) {
            if ($_GET) {
                if (!$_GET[$key]) {
                    $_GET[$key] = $value;
                }
            } else {
                global $_GET;
                $_GET = [
                    $key => $value,
                ];
            }
        }
    }
    public function parse() {
        $this->_parse_get_params();
    }
}