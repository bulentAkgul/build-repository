<?php

namespace Bakgul\BuildRepo\Services\BuilderServices;

use Bakgul\BuildRepo\Tasks\HandleAssetBundler;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Standalone;
use Bakgul\Kernel\Tasks\CompleteFolders;
use Bakgul\PackageGenerator\Functions\CreateRouteFiles;

class RootService
{
    public static function create()
    {
        self::test();
        self::routes();
        self::bundler();
        self::serviceProviders();
    }

    private static function test()
    {
        CompleteFolders::_(base_path('tests/' . Settings::folders('suite-container')), false);
    }

    private static function routes()
    {
        if (Standalone::true()) return;

        CreateRouteFiles::_();

        $file = Path::glue([base_path(), 'routes', 'web.php']);

        if (file_exists($file)) {
            file_put_contents($file, PHP_EOL . "Route::get('', fn () => view('web'));" . PHP_EOL, FILE_APPEND);
        }
    }

    private static function serviceProviders(): void
    {
        if (Standalone::true()) return;

        $name = 'RouteServiceProvider';

        copy(
            Path::glue([__DIR__, '..', '..', '..', 'stubs', 'packagify', "{$name}.stub"]),
            Path::glue([app_path(), 'Providers', "{$name}.php"])
        );
    }

    private static function bundler(): void
    {
        match (Settings::main('bundler')) {
            'mix' => HandleAssetBundler::mix(),
            default => HandleAssetBundler::vite()
        };
    }
}
