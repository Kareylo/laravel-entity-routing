<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\RouteParameter;

/**
 * Uses the value explicitly given by the caller for the parameter.
 */
final class ExtraValueResolver implements ParameterResolver
{
    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        return Resolution::of($extra[$parameter->name] ?? null);
    }
}
