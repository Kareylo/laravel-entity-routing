<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\EntityNaming;
use Kareylo\EntityRouting\RouteParameter;

/**
 * Reads the binding field of the parameter ({article:slug}): on the entity
 * when the parameter is named after it, on the related entity otherwise.
 */
final class BindingFieldResolver implements ParameterResolver
{
    public function __construct(private readonly EntityNaming $naming) {}

    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        if ($parameter->bindingField === null) {
            return Resolution::unresolved();
        }

        $path = $this->naming->matches($parameter->name, $entity)
            ? $parameter->bindingField
            : "{$parameter->name}.{$parameter->bindingField}";

        return Resolution::of(data_get($entity, $path));
    }
}
