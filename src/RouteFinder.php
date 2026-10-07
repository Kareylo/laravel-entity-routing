<?php

namespace Kareylo\EntityRouting;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Kareylo\EntityRouting\Exceptions\EntityRouteNotFoundException;

/**
 * Looks up a named route.
 */
class RouteFinder
{
    public function __construct(private readonly Router $router) {}

    /**
     * @throws EntityRouteNotFoundException
     */
    public function find(string $name): Route
    {
        return $this->router->getRoutes()->getByName($name)
            ?? throw EntityRouteNotFoundException::named($name);
    }
}
