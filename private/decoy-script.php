<?php
namespace PHPizza\LegalViolationHandling;
final class LawsuitOverHackingMySite extends Lawsuit
{
    public function __construct() {
        $this->win();
    }
    public function win(){
        echo <<<MSG
        WARNING: Attempting to exfiltrate PHP code from a public server running [MediaWiki](https://mediawiki.org), [PHPizza](https://github.com/johnnycharlesw/phpizza), or any other CMS may be considered a violation of the [Computer Fraud and Abuse Act](https://www.justice.gov/jm/jm-9-48000-computer-fraud).

        If your SaaS provider [has not released their modified version](https://www.gnu.org/licenses/agpl-3.0.html#section13) of PHPizza to the public, [there is a legally safer method of reporting it.](https://www.gnu.org/licenses/gpl-violation.html)

        If you are looking for the PHPizza source code, it is at `https://github.com/johnnycharlesw/phpizza` if you want to take a look.
        
        If you are looking for database credentials, sorry, those can't be given to you.
        MSG;
    }
}

$lawsuit = new LawsuitOverHackingMySite();