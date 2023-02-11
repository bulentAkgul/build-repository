<?php

namespace Bakgul\BuildRepo\Services;

use Bakgul\BuildRepo\Services\BuilderServices\AppService;
use Bakgul\BuildRepo\Services\BuilderServices\ClientService;
use Bakgul\BuildRepo\Services\BuilderServices\RootService;
use Bakgul\BuildRepo\Services\BuilderServices\ViewService;
use Bakgul\BuildRepo\Tasks\HandleTestSuites;
use Bakgul\BuildRepo\Tasks\Prepare;
use Bakgul\BuildRepo\Tasks\ProtectAdminApp;

class BuilderService
{
    public static function create(): void
    {
        Prepare::_();
        AppService::create();
        ClientService::create();
        ViewService::register();
        RootService::create();
        HandleTestSuites::_();
        ProtectAdminApp::_();
    }
}
