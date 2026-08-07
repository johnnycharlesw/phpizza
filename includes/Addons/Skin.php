<?php
namespace PHPizza\Addons;
use PHPizza\Rendering\Pizzadown;

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
        

        $markdown = @file_get_contents($this->assetPath("/parts/$type.md"));
        if ($markdown == "") {
            $markdown = "<div></div>";
        }
        
        $parsed=$parsedown->templateText($markdown,$this->get_template_variables_as_array());
        return $parsed;
    }

    public function get_component(string $type){
        return $this->_get_component($type, $this->get_template_variables_as_array());
    }

    public function get_powered_by_image_name_by_id($id) {
        return '/assets/os-branding/poweredby_' . $id . '.png';
    }

    public function get_os_url($id) {
        $urls = [
            'unix' => 'https://unix.org',
            'linux' => 'https://kernel.org',
            'debian' => 'https://debian.org',
            'windows' => 'https://www.microsoft.com/en-US/windows',
            'reactos' => 'https://reactos.org/',
            'freebsd' => 'https://freebsd.org',
            'openbsd' => 'https://openbsd.org',
            'niche' => 'https://github.com/search?q=operating+system&type=repositories'
        ];
        return $urls[$id];
    }

    public function get_os_powered_by_result($id){
        return [
            'name' => $id,
            'image' => $this->get_powered_by_image_name_by_id($id),
            'url' => $this->get_os_url($id)
        ];
    }

    public function get_os_powered_by(){
        $isUnix = is_dir('/') && is_dir('/usr') && is_dir('/usr/bin') && is_dir('/usr/sbin');
        $isWindowsLike = is_dir('C:/') && (is_dir('C:/Windows') || is_dir('C:/ReactOS'));
        $phpOsFamily = PHP_OS_FAMILY;

        // Unix-like operating systems
        if ($isUnix) {
            if ($phpOsFamily == "Linux") {
                // Linux: it may or may not be Debian
                if (is_file('/etc/os-release')) {
                    $osRelease = file_get_contents('/etc/os-release');
                    $isDebian = preg_match('/(debian|raspbian|ubuntu)/', $osRelease) && @is_file('/usr/bin/apt');
                    return $this->get_os_powered_by_result( $isDebian ? 'debian' : 'linux');
                } else {
                    return $this->get_os_powered_by_result('linux');
                }
            } elseif ($phpOsFamily == "Darwin") {
                return $this->get_os_powered_by_result('apple');
            } elseif ($phpOsFamily == "BSD") {
                $uname = strtolower(php_uname('s'));
                return $this->get_os_powered_by_result($uname ?? 'unix');
            }

            return $this->get_os_powered_by_result('unix');
        }

        // Windows-like operating systems
        if ($isWindowsLike) {
            # Check for ReactOS
            if (is_file('C:/Windows/System32/notevil.exe') || is_file('C:/ReactOS/system32/notevil.exe')) {
                # It is ReactOS, display the correct badge
                return $this->get_os_powered_by_result('reactos');
            } else {
                return $this->get_os_powered_by_result('windows');
            }
        }

        // Anything else-display a niche OS badge
        return $this->get_os_powered_by_result('niche');

    }

    public function get_template_variables_as_array(){
        global $sitename, $siteLogoPath, $copyrightInfo, $licenseInfo, $siteLanguage, $siteTheme, $homepageName, $poweredByImageURL, $guestUsername;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $osPoweredBy = $this->get_os_powered_by();
        $os = $osPoweredBy['name'];
        $osPoweredByImage = $osPoweredBy['image'];
        $osPoweredByUrl = $osPoweredBy['url'];
        $username=isset($_SESSION['username']) ? $_SESSION['username'] : $guestUsername;
        if ($username == $guestUsername){
            $userChangePage="index.php?title=PHPizza:UserLogin";
            $changeUserButtonText="Log In";
        } else {
            $userChangePage="index.php?title=PHPizza:UserLogout";
            $changeUserButtonText="Log Out";
        }
        global $hooks;
        $rendererVarInsertionHooks = $hooks['renderer_var_insertion'];
        $vars = [
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
            'osPoweredByImageUrl' => $osPoweredByImage,
            'osPoweredByLinkUrl' => $osPoweredByUrl,
            'osName' => $os
        ];
        if (count($rendererVarInsertionHooks) > 0) {
            foreach ($rendererVarInsertionHooks as $hook) {
                $vars = array_merge_recursive($vars, $hook());
            }
        }
        return $vars;
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

    public function get_sidebar2() {
        return $this->get_component('sidebar2');
    }

    public function render_email(string $emailContentMd){
        $email = "";
        
    }
}
