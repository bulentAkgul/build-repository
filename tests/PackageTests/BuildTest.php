<?php

namespace Bakgul\BuildRepo\Tests\PackageTests;

use Bakgul\Kernel\Tests\TestServices\TestDataService;
use Bakgul\Kernel\Tests\TestTasks\SetupTest;
use Bakgul\Kernel\Tests\TestCase;

class BuildTest extends TestCase
{
    /** @test */
    public function build_sp()
    {
        (new SetupTest)(TestDataService::case('sp'), true);

        $this->artisan('build-pl');

        $this->assertFileExists(base_path('config/awesome.php'));
        $this->assertFileExists(base_path('src/MyAwesomePackageServiceProvider.php'));
    }

    /** @test */
    public function build_sl()
    {
        (new SetupTest)(TestDataService::case('sl'), true);

        $this->artisan('build-pl');

        $this->assertFileExists(base_path('resources/app/styles/utilities/color.scss'));
        $this->assertFileExists(base_path('storage/logs/packagify/undo'));
    }

    /** @test */
    public function build_pl()
    {
        (new SetupTest)(TestDataService::case('pl'), true);

        $this->artisan('build-pl');

        $this->assertFileExists(base_path('packages'));
        $this->assertFileExists(base_path('tests/TestClasses/Feature'));
    }
}
