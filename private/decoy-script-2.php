<?php
namespace PHPizza\LegalViolationHandling;
final class LawsuitOverHackingMySite extends Lawsuit
{
    public function __construct() {
        $this->win();
    }
    public function win(){
        echo <<<MSG
        Dear anonymous criminal,

        It appears you have attempted to steal source code from a live server. That is not allowed and may result in punishment under the CFAA.

        If you are looking for the PHPizza source code, it is at `https://github.com/johnnycharlesw/phpizza` if you want to take a look.
        
        If you are looking for database credentials, sorry, those can't be given to you.

        Sincerely,
        @johnnycharlesw

        P.S. If this was not a false alarm, I dare you to tell Santa when it is near Christmas time. He has a list, you know.
        MSG;
    }
}

$lawsuit = new LawsuitOverHackingMySite();