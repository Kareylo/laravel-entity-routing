<?php

namespace Kareylo\EntityRouting\Resolution;

use Illuminate\Contracts\Routing\UrlRoutable;
use Kareylo\EntityRouting\EntityNaming;
use Kareylo\EntityRouting\RouteParameter;

/**
 * Uses the route key of a UrlRoutable entity when the parameter is named
 * after it ({article} for Article).
 */
final class RouteKeyResolver implements ParameterResolver
{
    public function __construct(private readonly EntityNaming $naming) {}

    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        if (! $entity instanceof UrlRoutable || ! $this->naming->matches($parameter->name, $entity)) {
            return Resolution::unresolved();
        }

        return Resolution::of($entity->getRouteKey());
    }
}
