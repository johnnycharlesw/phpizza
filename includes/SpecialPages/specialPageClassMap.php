<?php
use PHPizza\SpecialPages\OGTestHomepage;
use PHPizza\SpecialPages\Editor;
use PHPizza\SpecialPages\CreateAccount;
use PHPizza\SpecialPages\UserLogin;
use PHPizza\SpecialPages\UserLogout;
use PHPizza\SpecialPages\SpecialPages;
use PHPizza\SpecialPages\Settings;

$specialPageClassMap = [
    "OGTestHomepage" => OGTestHomepage::class,
    "UserLogin" => UserLogin::class,
    "SpecialPages" => SpecialPages::class,
    "UserLogout" => UserLogout::class,
    "CreateAccount" => CreateAccount::class,
    "MistralBackedAgentRenderer" => MistralBackedAgentRenderer::class,
    "Editor" => Editor::class,
    "SiteSettings" => Settings::class
];