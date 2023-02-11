<?php

namespace Bakgul\BuildRepo\Services;

use Bakgul\BuildRepo\Services\PackagifyServices\AppService;
use Bakgul\BuildRepo\Services\PackagifyServices\ClientService;
use Bakgul\BuildRepo\Services\PackagifyServices\RootService;
use Bakgul\BuildRepo\Services\PackagifyServices\ViewService;
use Bakgul\BuildRepo\Tasks\HandleTestSuites;
use Bakgul\BuildRepo\Tasks\Prepare;
use Bakgul\BuildRepo\Tasks\ProtectAdminApp;

class PackagifyService
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
