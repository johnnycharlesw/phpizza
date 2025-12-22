<?php

use PHPizza\SpecialPages\AdminPanel;
use PHPizza\SpecialPages\OGTestHomepage;
use PHPizza\SpecialPages\CreateAccount;
use PHPizza\SpecialPages\UserLogin;
use PHPizza\SpecialPages\UserLogout;
use PHPizza\SpecialPages\SpecialPages;
use PHPizza\SpecialPages\MistralBackedAgentRenderer;

$specialPageClassMap = [
    "OGTestHomepage" => OGTestHomepage::class,
    "UserLogin" => UserLogin::class,
    "SpecialPages" => SpecialPages::class,
    "UserLogout" => UserLogout::class,
    "CreateAccount" => CreateAccount::class,
    "MistralBackedAgentRenderer" => MistralBackedAgentRenderer::class,
    "AdminPanel" => AdminPanel::class,
];