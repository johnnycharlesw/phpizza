<?php
namespace PHPizza\LegalViolationHandling;
final class LawsuitOverHackingMySite extends Lawsuit
{
    public function __construct() {
        $this->win();
    }
    public function win(){
        echo <<<MSG
        Clearly, you didn't listen to the previous warnings, so I guess I'll just say it.  

        Maybe close down the shop over Tor and apply for a job as a white hat.    

        Hacking others' sites is unacceptable regardless of the legal consequences, ever heard "Treat others the way you want to be treated"?  

        Would YOU want to get hacked by an anonymous stranger? I don't know about you, but I certainly wouldn't.  

        So maybe don't hack others as an anonymous stranger. There. It's stated.
        MSG;
    }
}

$lawsuit = new LawsuitOverHackingMySite();