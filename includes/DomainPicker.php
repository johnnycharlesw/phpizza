<?php
namespace PHPizza;
class DomainPicker {
    public $techTLDs=['net','tech','dev'];
    public $nonProfitTLDs=['org'];
    public $forProfitTLDs=['com', 'biz'];
    public $personalSiteTLDs=['name','me'];
    public $onBrandFallbackTLDs=['pizza'];
    public $genericTLDs = ['page','site','xyz'];
    private $tldListMapping;

    public function __construct(){
        $this->tldListMapping = [
            'techSite' => $this->techTLDs,
            'nonProfit' => $this->nonProfitTLDs,
            'forProfit' => $this->forProfitTLDs,
            'personalSite' => $this->personalSiteTLDs,
            'other' => $this->genericTLDs,
        ];
    }


    public function generateDomainSuggestion(string $siteType, int $selectionID){
        global $sitename;
        $lowercasedSitename=strtolower($sitename);
        
        $domainInvalidChars=[
            " ", # space
            "   ", # tab
            "\b", # backspace
            "\n", # Linux/macOS (10.x and later) newline
            "\r", # Classic Mac OS newline/other piece of Windows newline
            "'", # apostrophe/string beginning
            "/", # fowardslash/server index
            ".", # subdomain beginning
        ];

        foreach ($domainInvalidChars as $forbiddenChar) {
            $lowercasedSitename=str_replace($forbiddenChar, "", $lowercasedSitename);
        }

        if (isset($this->tldListMapping[$siteType][$selectionID])){
            $tld=$this->tldListMapping[$siteType][$selectionID];
        }else{
            $tld=$this->onBrandFallbackTLDs[0];
        }

        return "$lowercasedSitename.$tld";

    }
}