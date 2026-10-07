<?php

namespace Kareylo\EntityRouting;

use Closure;
use Illuminate\Routing\UrlGenerator;

/**
 * Laravel's url generator, accepting an entity under the reserved "_entity"
 * route parameter. Every other call is left to the native generator.
 */
class EntityAwareUrlGenerator extends UrlGenerator
{
    public const ENTITY_PARAMETER = '_entity';

    /**
     * Resolves the entity url generator lazily, since it depends on this one.
     *
     * @var Closure(): EntityUrlGenerator
     */
    private Closure $entities;

    /**
     * Copy the state of an existing generator (forced root, resolvers,
     * defaults...) into an entity aware one.
     *
     * @param  Closure(): EntityUrlGenerator  $entities
     */
    public static function fromGenerator(UrlGenerator $url, Closure $entities): self
    {
        $copy = new self($url->routes, $url->request, $url->assetRoot);

        foreach (get_object_vars($url) as $property => $value) {
            $copy->{$property} = $value;
        }

        // The route generator points back at the original generator and holds
        // the url defaults: rebuild it for the copy.
        $copy->routeGenerator = null;
        $copy->defaults($url->getDefaultParameters());
        $copy->entities = $entities;

        return $copy;
    }

    /**
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        if (! is_string($name) || ! is_array($parameters) || ! array_key_exists(self::ENTITY_PARAMETER, $parameters)) {
            return parent::route($name, $parameters, $absolute);
        }

        $entity = $parameters[self::ENTITY_PARAMETER];
        unset($parameters[self::ENTITY_PARAMETER]);

        return ($this->entities)()->generate($name, $entity, $parameters, $absolute);
    }
}
