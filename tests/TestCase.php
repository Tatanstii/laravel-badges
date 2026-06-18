<?php

namespace Insynnia\Badges\Tests;

use Insynnia\Badges\BadgesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BadgesServiceProvider::class];
    }
}
