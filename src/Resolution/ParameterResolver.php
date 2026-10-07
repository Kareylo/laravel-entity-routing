<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\RouteParameter;

/**
 * One rule for finding the value of a route parameter on an entity.
 */
interface ParameterResolver
{
    public function resolve(RouteParameter $parameter, mixed $entity): Resolution;
}
