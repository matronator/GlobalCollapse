<?php

namespace App\Extensions\Latte;

use Latte\Compiler;
use Latte\Macros\MacroSet;

class AssetExtension {

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

    public static function asset(string $asset, string $module = 'front'): string
    {
        $rawManifest = file_get_contents(WWW_DIR . '/dist/' . $module . '/asset-manifest.json');
        $manifest = json_decode($rawManifest, true);

        $path = '/dist/' . $module . '/' . $manifest[$asset];

        return $path;
    }
}
