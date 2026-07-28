<?php

use PHPizza\SpecialPages\AdminPanel\AdminPanel;
use PHPizza\SpecialPages\CreateAccount;
use PHPizza\SpecialPages\DestroySessionToken;
use PHPizza\SpecialPages\ExceptionTest;
use PHPizza\SpecialPages\UserLogin;
use PHPizza\SpecialPages\UserLogout;
use PHPizza\SpecialPages\SpecialPages;
use PHPizza\SpecialPages\MistralBackedAgentRenderer;
use PHPizza\SpecialPages\SwitchUser;
use PHPizza\SpecialPages\VideoRenderer;

$specialPageClassMap = [
    "UserLogin" => UserLogin::class,
    "SpecialPages" => SpecialPages::class,
    "UserLogout" => UserLogout::class,
    "CreateAccount" => CreateAccount::class,
    "MistralBackedAgentRenderer" => MistralBackedAgentRenderer::class,
    "AdminPanel" => AdminPanel::class,
    "Debug/ExceptionTest" => ExceptionTest::class,
    "DestroySessionToken" => DestroySessionToken::class,
    "SwitchUser" => SwitchUser::class,
    "VideoRenderer" => VideoRenderer::class
];