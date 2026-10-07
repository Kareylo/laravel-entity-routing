<?php

namespace Kareylo\EntityRouting;

use Illuminate\Contracts\Routing\UrlGenerator;
use Kareylo\EntityRouting\Exceptions\EntityRouteNotFoundException;
use Kareylo\EntityRouting\Exceptions\InvalidEntityRouteParameterException;
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
     * @throws InvalidEntityRouteParameterException
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
                if ($parameter->inDomain && ! $this->isHostValue($resolution->value())) {
                    throw InvalidEntityRouteParameterException::forDomain($route, $parameter->name);
                }

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

    /**
     * Domain values may only contain host characters, so they cannot move
     * the url to another host ("evil.com/", "user@evil.com"...).
     */
    private function isHostValue(mixed $value): bool
    {
        return is_scalar($value) && preg_match('/^[A-Za-z0-9.-]+$/', (string) $value) === 1;
    }
}
