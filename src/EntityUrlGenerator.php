<?php

namespace Kareylo\EntityRouting;

use Illuminate\Contracts\Routing\UrlGenerator;
use Kareylo\EntityRouting\Exceptions\EntityRouteNotFoundException;
use Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException;
use Kareylo\EntityRouting\Resolution\ParameterResolver;

/**
 * Generates the url of a named route, filling its parameters from an entity.
 */
class EntityUrlGenerator
{
    public function __construct(
        private readonly RouteFinder $routes,
        private readonly RouteParameterReader $parameters,
        private readonly ParameterResolver $resolver,
        private readonly UrlGenerator $url,
    ) {}

    /**
     * @param  array<array-key, mixed>  $extra  explicit values, taking precedence over the entity;
     *                                          those matching no placeholder become the query string
     *
     * @throws EntityRouteNotFoundException
     * @throws MissingEntityRouteParameterException
     */
    public function generate(string $name, mixed $entity, array $extra = [], bool $absolute = true): string
    {
        $route = $this->routes->find($name);
        $values = [];
        $missing = [];
        $query = $extra;

        foreach ($this->parameters->read($route) as $parameter) {
            $resolution = $this->resolver->resolve($parameter, $entity, $extra);
            unset($query[$parameter->name]);

            if ($resolution->resolved()) {
                $values[$parameter->name] = $resolution->value();
            } elseif (! $parameter->optional) {
                $missing[] = $parameter->name;
            }
        }

        if ($missing !== []) {
            throw MissingEntityRouteParameterException::forEntity($route, $missing, $entity);
        }

        return $this->url->route($name, $values + $query, $absolute);
    }
}
