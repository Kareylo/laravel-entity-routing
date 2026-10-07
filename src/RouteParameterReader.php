<?php

namespace Kareylo\EntityRouting;

use Illuminate\Routing\Route;

/**
 * Reads the placeholders of a route, domain placeholders first.
 */
class RouteParameterReader
{
    /**
     * @return list<RouteParameter>
     */
    public function read(Route $route): array
    {
        preg_match_all('/\{(\w+)\?\}/', $route->getDomain().$route->uri(), $matches);
        $optional = $matches[1];

        preg_match_all('/\{(\w+)\??\}/', (string) $route->getDomain(), $matches);
        $domain = $matches[1];

        /** @var list<string> $names */
        $names = $route->parameterNames();

        return array_map(
            fn (string $name) => new RouteParameter(
                $name,
                in_array($name, $optional, true),
                $route->bindingFieldFor($name),
                in_array($name, $domain, true),
            ),
            $names,
        );
    }
}
