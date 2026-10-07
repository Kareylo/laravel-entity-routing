<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\EntityAttributes;
use Kareylo\EntityRouting\RouteParameter;

/**
 * Reads the entity attribute named after the parameter.
 */
final class AttributeResolver implements ParameterResolver
{
    public function __construct(private readonly EntityAttributes $attributes = new EntityAttributes) {}

    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        return Resolution::of($this->attributes->get($entity, $parameter->name));
    }
}
