<?php

use Kareylo\EntityRouting\EntityUrlGenerator;

if (! function_exists('entity_route')) {
    /**
     * Generate the url of a named route, filling its parameters from an entity.
     *
     * @param  array<array-key, mixed>  $extra  explicit values, taking precedence over the entity
     */
    function entity_route(string $name, mixed $entity, array $extra = [], bool $absolute = true): string
    {
        return app(EntityUrlGenerator::class)->generate($name, $entity, $extra, $absolute);
    }
}
