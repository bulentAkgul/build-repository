<?php

namespace Bakgul\BuildRepo\Services\PackagifyServices;

use Bakgul\BuildRepo\Functions\MakeFolder;
use Bakgul\BuildRepo\Tasks\CreateFiles;
use Bakgul\Kernel\Helpers\Settings;

class ClientService
{
    private static $root;

    public static function create()
    {
        if (self::isCreatorMissing()) return;

        self::setRoot();

        foreach (Settings::apps() as $key => $app) {
            $app = [...$app, 'key' => $key];

            MakeFolder::_(self::$root, $app['folder']);

            array_map(
                fn ($type) => CreateFiles::_($app, $type),
                ['css', 'js', 'view']
            );
        }
    }

    private static function isCreatorMissing(): bool
    {
        return !class_exists("\Bakgul\ResourceCreator\ResourceCreatorServiceProvider");
    }

    private static function setRoot()
    {
        self::$root = MakeFolder::_(base_path('resources'), Settings::folders('apps'));
    }
}
