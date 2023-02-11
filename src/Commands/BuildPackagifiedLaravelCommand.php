<?php

namespace Bakgul\BuildRepo\Commands;

use Bakgul\BuildRepo\Services\BuilderService;
use Bakgul\FileHistory\Concerns\HasHistory;
use Bakgul\Kernel\Concerns\HasPreparation;
use Bakgul\Kernel\Concerns\HasRequest;
use Bakgul\Kernel\Tasks\SimulateArtisanCall;
use Bakgul\Kernel\Helpers\Standalone;
use Illuminate\Console\Command;

class BuildPackagifiedLaravelCommand extends Command
{
    use HasHistory, HasPreparation, HasRequest;

    protected $signature = 'build-pl';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->prepareRequest();

        $this->logFile();

        Standalone::isPackage()
            ? $this->createPackage()
            : BuilderService::create();
    }

    private function createPackage(): void
    {
        (new SimulateArtisanCall)(
            ['command' => 'create:package', 'name' => null, 'root' => null, 'dev' => false],
            'package'
        );
    }
}
