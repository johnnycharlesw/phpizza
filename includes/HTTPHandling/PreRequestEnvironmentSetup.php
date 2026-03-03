<?php
namespace PHPizza\HTTPHandling;

class PreRequestEnvironmentSetup {
    private GETParameterParser $gp;
    private POSTParameterParser $pp;
    public string $url;
    public function __construct($url) {
        $this->url=$url;
        $this->gp = new GETParameterParser($url);
        $this->pp = new POSTParameterParser();
    }

    public function parse_parameters() {
        $this->gp->parse();
        $this->pp->parse();
    }
}