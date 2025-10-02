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
            $parsedown = new \Parsedown();
        }
        catch (e){
            include 'vendor/autoload.php';
            $parsedown = new \Parsedown();
        }
        
        $markdown = file_get_contents($this->assetPath("parts/$type.md"));
        $parsed=$parsedown->text($markdown);
        foreach ($vars as $key => $value) {
            $parsed=str_replace("{{$key}}", $value, $parsed);
        }
        return $parsed;
    }

    public function get_component(string $type){
        return $this->_get_component($type, $this->get_template_variables_as_array());
    }

    public function get_template_variables_as_array(){
        global $sitename, $siteLogoPath, $copyrightInfo, $licenseInfo, $siteLanguage, $siteTheme;
        return [
            'sitename' => $sitename,
            'siteLogoPath' => $siteLogoPath,
            'copyrightInfo' => $copyrightInfo,
            'licenseInfo' => $licenseInfo,
            'siteLanguage' => $siteLanguage,
            'siteTheme' => $siteTheme,
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
