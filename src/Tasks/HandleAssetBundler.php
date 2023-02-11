<?php

namespace Bakgul\BuildRepo\Tasks;

use Bakgul\FileContent\Functions\MakeFile;
use Bakgul\Kernel\Helpers\Arr;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Str;

class HandleAssetBundler
{
    public static function vite()
    {
        MakeFile::_(self::viteRequest(), false, false);
    }

    private static function viteRequest(): array
    {
        return [
            'attr' => [
                'path' => base_path(),
                'file' => 'vite.config.js',
                'stub' => 'vite.config.stub',
                'job' => 'packagify',
                'variation' => '',
            ],
            'map' => [
                ...self::viteSpa(),
                'inputs' => self::viteInputs(),
                'host' => parse_url(config('app.url'))['host'],
            ]
        ];
    }

    public static function viteInputs(): string
    {
        $inputs = [];

        $base = 'resources/' . Settings::folders('apps');

        foreach (Settings::apps() as $name => $specs) {
            $input = "{$base}/{$name}";

            foreach (['js', 'css'] as $type) {
                $inputs[] = implode('', [
                    "'",
                    "{$input}/",
                    Settings::folders($type),
                    "/{$name}",
                    $type == 'js' ? 'js' : Settings::resources(Settings::main('css') . '.extension'),
                    "',"
                ]);
            }
        }

        return implode(PHP_EOL . str_repeat(' ', 8), ['', ...$inputs, '']) . str_repeat(' ', 6);
    }

    private static function viteSpa(): array
    {
        $vue = in_array('vue', Arr::pluck(Settings::apps(), 'type'));

        return [
            'import_spa' => $vue ? 'import vue from "@vitejs/plugin-vue";' . PHP_EOL : '',
            'plugin_spa' => $vue ? 'vue({template: {transformAssetUrls: {base: null,includeAbsolute: false,},},}),' : '',
            'ssr_spa' => $vue ? '"vue"' : ''
        ];
    }

    public static function mix()
    {
        $content = ["const mix = require('laravel-mix');", "", "mix", "  .disableNotifications()"];

        $apps = Settings::apps();

        file_put_contents(base_path('webpack.mix.js'), implode(PHP_EOL, array_merge($content, self::mixStyles($apps), self::mixScripts($apps))));
    }

    private static function mixStyles($apps)
    {
        $container = Settings::folders('apps');
        $folder = Settings::folders('css');
        $style = Settings::main('css');
        $ext = Settings::resources("{$style}.extension");

        $styles = [];

        foreach ($apps as $app) {
            $from = ['resources', $container, $app['folder'], $folder, "{$app['folder']}.{$ext}"];

            if (!file_exists(base_path(Path::glue($from)))) continue;

            $styles[] = "  .{$style}(" . Str::enclose(Path::glue($from, '/'), 'dq') . ', "public/css/' . $app['folder'] . '.css")';
        }

        return $styles;
    }

    private static function mixScripts($apps)
    {
        $container = Settings::folders('apps');
        $folder = Settings::folders('js');

        $scripts = [];

        foreach ($apps as $app) {
            $ext = Settings::resources("js.extension");
            $method = $app['type'] == 'react' ? 'react' : $ext;

            $from = ['resources', $container, $app['folder'], $folder, "{$app['folder']}.{$ext}"];

            if (!file_exists(base_path(Path::glue($from)))) continue;

            $scripts[] = implode('', [
                "  .{$method}(",
                Str::enclose(Path::glue($from, '/'), 'dq'),
                ', "public/js/',
                "{$app['folder']}.js",
                '")',
                $app['type'] != 'blade' ? '.extract()' : ''
            ]);
        }

        return $scripts;
    }
}
