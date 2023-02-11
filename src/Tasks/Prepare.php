<?php

namespace Bakgul\BuildRepo\Tasks;

use Bakgul\BuildRepo\Functions\MakeFolder;
use Bakgul\Kernel\Helpers\Arr;
use Bakgul\Kernel\Helpers\Folder;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Standalone;
use Bakgul\Kernel\Helpers\Str;
use Bakgul\Kernel\Tasks\CompleteFolders;

class Prepare
{
    public static function _()
    {
        self::logFolders();
        self::resources();
        self::packages();
        self::copy();
    }

    private static function logFolders()
    {
        $base = Path::glue([storage_path(), 'logs', 'packagify', '']);

        array_map(fn ($x) => CompleteFolders::_("{$base}{$x}"), ['redo', 'undo']);
    }

    private static function resources()
    {
        file_exists(base_path('resources'))
            ? Folder::refresh(base_path('resources'))
            : MakeFolder::_(base_path('resources'));
    }

    private static function packages()
    {
        if (Standalone::isLaravel()) return;

        foreach (Settings::roots() as $root) {
            MakeFolder::_(base_path(Settings::folders('packages')), $root['folder']);
        }
    }

    private static function copy()
    {
        $src = Path::glue([__DIR__, '..', '..', 'files']);

        if (Standalone::true()) return self::copyTestConfigFiles($src);

        foreach (Folder::files($src) as $file) {
            if (str_contains($file, 'test_config')) continue;

            $paste = base_path(str_replace('.stub', '', explode('files' . DIRECTORY_SEPARATOR, $file)[1]));

            CompleteFolders::_(Str::dropTail($paste));

            copy($file, $paste);
        }

        self::copyTestConfigFiles($src);
    }

    private static function copyTestConfigFiles(string $src)
    {
        foreach (array_keys(Settings::tests()) as $lang) {
            $config = Settings::tests($lang, 'config_file');

            $path = Arr::first(Folder::files(
                Path::glue([$src, 'test_config']),
                fn ($x) => str_contains($x, $config)
            ));

            copy($path, base_path($config));
        }
    }
}
