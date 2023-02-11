<?php

namespace Bakgul\BuildRepo\Tasks;

use Bakgul\Kernel\Functions\CreateFileRequest;
use Bakgul\Kernel\Helpers\Arr;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Tasks\SimulateArtisanCall;

class ProtectAdminApp
{
    const MIDDLEWARE = 'admin-app-can-be-used-by-current-user';

    public static function _()
    {
        if (self::noAdminApp()) return;

        self::setEvaluator(false);

        self::createMiddleware();

        self::setEvaluator(true);
    }

    private static function noAdminApp()
    {
        return Arr::hasNot(Settings::apps(), 'admin');
    }

    private static function setEvaluator(bool $value)
    {
        Settings::set('evaluator.evaluate_commands', $value);
    }

    private static function createMiddleware()
    {
        (new SimulateArtisanCall)(CreateFileRequest::_([
            'name' => self::MIDDLEWARE,
            'type' => 'middleware',
        ]));
    }
}
