<?php

namespace Kareylo\EntityRouting;

/**
 * A placeholder of a route, as read from its domain and uri.
 */
final class RouteParameter
{
    public function __construct(
        public readonly string $name,
        public readonly bool $optional,
        public readonly ?string $bindingField,
        public readonly bool $inDomain = false,
    ) {}
}
