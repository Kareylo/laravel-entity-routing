<?php

namespace Kareylo\EntityRouting\Contracts;

use Closure;

/**
 * Lets an entity declare how its route parameters are resolved.
 */
interface ProvidesRouteParameters
{
    /**
     * Map a parameter name to a dot notation path on the entity, or to a
     * closure receiving the entity.
     *
     * @return array<string, string|Closure>
     */
    public function routeParameters(): array;
}
