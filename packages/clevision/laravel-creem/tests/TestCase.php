<?php

namespace Clevision\Creem\Tests;

use Clevision\Creem\CreemServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CreemServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('creem.api_key', 'test_api_key_12345');
        $app['config']->set('creem.webhook_secret', 'test_webhook_secret');
        $app['config']->set('creem.test_mode', true);
    }
}
