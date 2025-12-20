<?php
namespace PHPizza\Addons;
# This file defines a class with hooks for extensions.
class PHPizzaExtensionAPI {
    public function __construct() {
        // Initialization code for the extension API can go here.
    }

    public function registerSpecialPage(string $pageName, string $className) {
        global $specialPageClassMap;
        $specialPageClassMap[$pageName] = $className;
    }

    public function unregisterSpecialPage(string $pageName) {
        global $specialPageClassMap;
        if (isset($specialPageClassMap[$pageName])) {
            unset($specialPageClassMap[$pageName]);
        }
    }

    public function getSpecialPages(): array {
        global $specialPageClassMap;
        return $specialPageClassMap;
    }

    public function unregisterHook(string $hookName) {
        global $hooks;
        if (isset($hooks[$hookName])) {
            unset($hooks[$hookName]);
        }
    }

    public function registerEmbedHandler(string $type, string $className) {
        global $embedTypeClassMapping;
        $embedTypeClassMapping[$type] = $className;
    }

    public function registerHook(string $hookName, callable $callback) {
        global $hooks;
        if (!isset($hooks[$hookName])) {
            $hooks[$hookName] = [];
        }
        $hooks[$hookName][] = $callback;
    }
}