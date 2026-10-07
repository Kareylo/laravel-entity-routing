<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\RouteParameter;

/**
 * One rule for finding the value of a route parameter on an entity.
 */
interface ParameterResolver
{
    /**
     * @param  array<array-key, mixed>  $extra  explicit values given by the caller
     */
    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution;
}
