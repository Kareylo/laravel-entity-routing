<?php

namespace Kareylo\EntityRouting\Exceptions;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Thrown when no route has the requested name.
 */
class EntityRouteNotFoundException extends RouteNotFoundException
{
    public static function named(string $name): self
    {
        return new self("Route [{$name}] not defined.");
    }
}
