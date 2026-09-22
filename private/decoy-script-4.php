<?php
namespace PHPizza\LegalViolationHandling;
final class LawsuitOverHackingMySite extends Lawsuit
{
    public function __construct() {
        $this->win();
    }
    public function win(){
        echo <<<MSG
        Clearly, you didn't listen to the previous warnings at all.

        Just delete the hacking tools, maybe switch to [Debian](https://debian.org) from Kali, and go about your day.  

        Maybe apply for a job or two. Maybe [contribute to the project](https://github.com/johnnycharlesw/phpizza/blob/main/CONTRIBUTING.md).

        The job market can't be THAT clogged, right?
        MSG;
    }
}

$lawsuit = new LawsuitOverHackingMySite();