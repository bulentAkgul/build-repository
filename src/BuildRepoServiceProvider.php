<?php

namespace Bakgul\BuildRepo;

use Bakgul\Kernel\Concerns\HasConfig;
use Illuminate\Support\ServiceProvider;

class BuildRepoServiceProvider extends ServiceProvider
{
    use HasConfig;

    public function boot()
    {
        $this->commands([
            \Bakgul\BuildRepo\Commands\BuildPackagifiedLaravelCommand::class,
        ]);
    }

    public function register()
    {
        $this->registerConfigs(__DIR__ . DIRECTORY_SEPARATOR . '..');
    }
}
