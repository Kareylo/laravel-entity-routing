<?php

namespace Kareylo\EntityRouting\Resolution;

use Illuminate\Routing\UrlGenerator;
use Kareylo\EntityRouting\RouteParameter;

/**
 * Falls back to the defaults set with URL::defaults().
 */
final class UrlDefaultsResolver implements ParameterResolver
{
    public function __construct(private readonly UrlGenerator $url) {}

    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        return Resolution::of($this->url->getDefaultParameters()[$parameter->name] ?? null);
    }
}
