<?php

namespace Kareylo\EntityRouting\Resolution;

use Closure;
use Kareylo\EntityRouting\Contracts\ProvidesRouteParameters;
use Kareylo\EntityRouting\RouteParameter;

/**
 * Uses the mapping the entity declares through ProvidesRouteParameters.
 */
final class EntityMappingResolver implements ParameterResolver
{
    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        if (! $entity instanceof ProvidesRouteParameters) {
            return Resolution::unresolved();
        }

        $mapping = $entity->routeParameters()[$parameter->name] ?? null;

        return match (true) {
            $mapping instanceof Closure => Resolution::of($mapping($entity)),
            is_string($mapping) => Resolution::of(data_get($entity, $mapping)),
            default => Resolution::unresolved(),
        };
    }
}
