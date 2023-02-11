<?php

namespace Bakgul\BuildRepo\Tasks;

use Bakgul\Kernel\Helpers\Test;
use Bakgul\PackageGenerator\Services\TestSuiteServices\PHPUnitBuilderService;

class HandleTestSuites
{
    public static function _()
    {
        foreach (Test::vendors() as $vendor) {
            match ($vendor) {
                'phpunit' => (new PHPUnitBuilderService(self::request()))(),
                default => null
            };
        }
    }

    private static function request()
    {
        return [
            'attr' => ['path' => base_path()],
            'map' => []
        ];
    }
}
