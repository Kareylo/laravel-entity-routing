<?php

namespace Kareylo\EntityRouting\Tests;

use Kareylo\EntityRouting\EntityRoutingServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [EntityRoutingServiceProvider::class];
    }
}
