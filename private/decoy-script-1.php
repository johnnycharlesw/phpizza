<?php
namespace PHPizza\LegalViolationHandling;
final class LawsuitOverHackingMySite extends Lawsuit
{
    public function __construct() {
        $this->settle();
    }
    public function settle(){
        echo <<<MSG
        Sorry, your dignity as a hacker has been encrypted and we'll demand $20 in DOGE to decrypt it (kidding, of course, but still, don't attempt to hack other people's sites)
        If you really want to pay anyways, pay to `DBDbikMMUPFSRpaKKt2ssaro9pa6BnK8sn`.

        If you are looking for the PHPizza source code, it is at `https://github.com/johnnycharlesw/phpizza` if you want to take a look.
        
        If you are looking for database credentials, sorry, those can't be given to you.
        MSG;
    }
}

$lawsuit = new LawsuitOverHackingMySite();