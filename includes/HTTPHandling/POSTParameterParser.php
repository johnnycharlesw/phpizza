<?php
namespace PHPizza\HTTPHandling;

class POSTParameterParser {
    public string $postdata;
    public function __construct() {
        $this->postdata = file_get_contents('php://input');
    }

    public function parse(){
        global $HTTP_RAW_POST_DATA;
        $HTTP_RAW_POST_DATA = $this->postdata;
        $params = json_decode($HTTP_RAW_POST_DATA);
        if ($_POST) {
            foreach ($params as $key => $value) {
                if (!$_POST[$key]) {
                    $_POST[$key] = $value;
                }
            }
        } else {
            global $_POST;
            $_POST = $params;
        }
    }
}