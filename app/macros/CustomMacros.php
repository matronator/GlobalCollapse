<?php

namespace App\Macros;

use Latte\Compiler;
use Latte\Macros\MacroSet;

abstract class CustomMacros extends MacroSet {

    public static function install(Compiler $compiler)
    {
        $set = new MacroSet($compiler);

        $set->addMacro(
            'asset',
            function ($node) {
                list($assetName, $moduleName) = explode(' ', $node->args);
                $rawManifest = file_get_contents(WWW_DIR . '/dist/' . $moduleName . '/asset-manifest.json');
                $manifest = json_decode($rawManifest, true);
                $path = '/dist/' . $moduleName . '/' . $manifest[$assetName];
                return "echo '$path';";
            }
        );

        $set->addMacro(
            'external',
            function ($node) {
                return "echo 'target=\"_blank\" rel=\"noopener\"'";
            }
        );
    }
}
