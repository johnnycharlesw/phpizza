<?php
namespace PHPizza\HTTPHandling;

use CurlHandle;
use PHPizza\UserManagement\UserDatabase;
use PHPizza\UserManagement\UserGroupDatabase;
use PHPizza\ConfigurationDatabase;

/**
 * Anonymity layer built into PHPizza to make Tor unnecessary for connections (thanks @ipqwery for your api used for information about the ip)
 */
class ClientIdentity
{
    private string $ip;
    private string $hostname;
    private int $port;
    public bool $https;
    private CurlHandle $curl;
    private UserGroupDatabase $groupdb;
    private UserDatabase $userdb;
    private ConfigurationDatabase $configdb;

    public string $countryCode;
    public string $city;
    public string $state;
    public string $timezone;

    public bool $isTor;
    public bool $isVpn;
    public bool $isMobileData;
    public bool $isProxy;
    public bool $isDatacenter;


    public function __construct() {
        // Connect to ipquery and initialize PHPizza APIs
        global $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $this->curl = curl_init("https://api.ipquery.io");
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 PHPizza/2026.5.31");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 13);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 78);
        $this->userdb = new UserDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->groupdb = new UserGroupDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->configdb = new ConfigurationDatabase($dbServer, $dbUser, $dbPassword, $dbName, $dbType);
        $this->register_settings();

        // obfuscate ip and hostname info
        $this->ip = $_SERVER["REMOTE_ADDR"];
        $_SERVER["REMOTE_ADDR"] = "0.0.0.0";
        $this->hostname = $_SERVER["REMOTE_HOST"] ?? gethostbyaddr($this->ip);
        $_SERVER["REMOTE_HOST"] = gethostname();

        // Retrieve HTTPS info and obfuscate it
        if (!empty($_SERVER["HTTPS"])) {
            $this->https = true;
        } else {
            $this->https = false;
        }
        $_SERVER["HTTPS"] = true;

        // Obfuscate remote port info
        $this->port = (int)$_SERVER["REMOTE_PORT"];
        if ($this->https) {
            $_SERVER["REMOTE_PORT"] = "443";
        } else {
            $_SERVER["REMOTE_PORT"] = "80";
        }

        // Set $_SERVER["SERVER_ADMIN"] to the email of the first admin user, we can't really trust the webserver to give an accurate email
        $adminGroup = $this->groupdb->get_user_group_by_name('admin');
        $firstAdminUserId = $adminGroup->members[0];
        $firstAdminUser = $this->userdb->get_user_by_id($firstAdminUserId);
        $firstAdminUserEmail = $firstAdminUser->email;
        $_SERVER["SERVER_ADMIN"] = $firstAdminUserEmail;

        // Query IPQuery
        $this->ipquery();
        $this->block_disallowed_clients();
    }

    private function register_settings(){
        $this->configdb->register_key('allowTor', false);
        $this->configdb->register_key('allowVPN', true);
        $this->configdb->register_key('allowMobileData', true);
        $this->configdb->register_key('allowDatacenter', false);
        $this->configdb->register_key('allowProxy', true);
        $this->configdb->register_key('enableIpquery', true);
    }

    public function ipquery(){
        global $enableIpquery;
        if (!isset($enableIpquery)) {
            $enableIpquery=true;
        }
        if (!$enableIpquery) {
            $this->isTor = false;
            $this->isVpn = false;
            $this->isMobileData = false;
            $this->isDatacenter = false;
            $this->isProxy = false;
            $this->countryCode = "US";
            $this->city="Albany";
            $this->state = "New York";
            $this->timezone = "America/New_York";
            return;
        }
        // Point ipquery to the ip
        curl_setopt($this->curl, CURLOPT_URL, "https://api.ipquery.io/" . $this->ip);
        // Query it
        $result = json_decode(curl_exec($this->curl), true);

        // Store information about the client
        $this->isTor = (bool)$result['risk']['is_tor'];
        $this->isVpn = (bool)$result['risk']['is_vpn'];
        $this->isMobileData = (bool)$result['risk']['is_mobile'];
        $this->isDatacenter = (bool)$result['risk']['is_datacenter'];
        $this->isProxy = (bool)$result['risk']['is_proxy'];


        // Store the information
        $this->countryCode = (string)$result['location']['country_code'];
        $this->city = (string)$result['location']['city'];
        $this->state = (string)$result['location']['state'];
        $this->timezone = (string)$result['location']['timezone'];
    }

    public function is_client_allowed(): bool {
        global $allowTor, $allowVPN, $allowMobileData, $allowDatacenter, $allowProxy, $isApi;
        $allowed = true;

        // Check for Tor
        if ($this->isTor && !$allowTor) {
            $allowed = false;
            throw new DisallowedClientException("Listen, PHPizza knows your IP isn't {$this->ip}. You are clearly using Tor Browser.");
        }

        // Check for VPNs
        if ($this->isVpn && !$allowVPN) {
            $allowed = false;
            throw new DisallowedClientException("Listen, PHPizza knows your IP isn't {$this->ip}. You are clearly using a VPN.");
        }

        // Check for proxies
        if ($this->isProxy && !$allowProxy) {
            $allowed = false;
            throw new DisallowedClientException("Listen, PHPizza knows your IP isn't {$this->ip}. You are clearly using a proxy.");
        }

        // Check for mobile data
        if ($this->isMobileData && !$allowMobileData) {
            $allowed = false;
            throw new DisallowedClientException("Sorry, no mobile data usage allowed here!");
        }

        // Check for datacenter requests
        if ($this->isDatacenter && !$allowDatacenter && !$isApi) {
            $allowed = false;
            throw new DisallowedClientException("Maybe use our actual API, developer.");
        }

        return $allowed;
    }

    public function block_disallowed_clients() {
        $allowed = $this->is_client_allowed();
        if (!$allowed) {
            throw new DisallowedClientException("This client is not allowed.");
        }
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
