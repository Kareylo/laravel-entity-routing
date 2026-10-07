<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\RouteParameter;

/**
 * Reads the entity attribute named after the parameter.
 */
final class AttributeResolver implements ParameterResolver
{
    public function resolve(RouteParameter $parameter, mixed $entity): Resolution
    {
        return Resolution::of(data_get($entity, $parameter->name));
    }
}
