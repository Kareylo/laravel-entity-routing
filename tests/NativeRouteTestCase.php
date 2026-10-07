<?php

namespace Kareylo\EntityRouting\Tests;

/**
 * Boots the package with the native route helper enabled.
 */
abstract class NativeRouteTestCase extends TestCase
{
    /**
     * Runs before service providers register, like a real config file,
     * unlike defineEnvironment().
     */
    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('entity-routing.native_route_helper', true);
    }
}
