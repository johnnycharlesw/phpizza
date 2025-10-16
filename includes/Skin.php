<?php
namespace PHPizza;

class Skin extends Addon
{
    public function __construct(string $name)
    {
        parent::__construct($name, 'skin');
    }

    public function get_skin_class() {
        $name=$this->name;
        $name=strtolower($name);
        return "phpizza-skin-$name";
    }

    private function _get_component(string $type, array $vars) {
        $name=$this->name;
        try {
            $parsedown = new Pizzadown();
        }
        catch (\Exception $e){
            include 'vendor/autoload.php';
            $parsedown = new Pizzadown();
        }
        

        $markdown = file_get_contents($this->assetPath("/parts/$type.md"));
        
        $parsed=$parsedown->templateText($markdown,$this->get_template_variables_as_array());
        return $parsed;
    }

    public function get_component(string $type){
        return $this->_get_component($type, $this->get_template_variables_as_array());
    }

    public function get_template_variables_as_array(){
        global $sitename, $siteLogoPath, $copyrightInfo, $licenseInfo, $siteLanguage, $siteTheme, $homepageName, $poweredByImageURL, $guestUsername;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $username=isset($_SESSION['username']) ? $_SESSION['username'] : $guestUsername;
        if ($username == $guestUsername){
            $userChangePage="index.php?title=PHPizza:UserLogin";
            $changeUserButtonText="Log In";
        } else {
            $userChangePage="index.php?title=PHPizza:UserLogout";
            $changeUserButtonText="Log Out";
        }
        return [
            'sitename' => $sitename,
            'siteLogoPath' => $siteLogoPath,
            'copyrightInfo' => $copyrightInfo,
            'licenseInfo' => $licenseInfo,
            'siteLanguage' => $siteLanguage,
            'siteTheme' => $siteTheme,
            'homePage' => $homepageName,
            'poweredByImageURL' => $poweredByImageURL,
            'userName' => $username,
            'userChangePage' => $userChangePage,
            'changeUserButtonText' => $changeUserButtonText,
        ];
    }

    

    public function get_header(){
        return $this->get_component('header');
    }

    public function get_sidebar(){
        return $this->get_component('sidebar');
    }

    public function get_footer(){
        return $this->get_component('footer');
    }
}
