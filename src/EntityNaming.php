<?php

namespace Kareylo\EntityRouting;

use Illuminate\Support\Str;

/**
 * Tells whether a route parameter is named after the entity class.
 */
class EntityNaming
{
    /**
     * True when the name is the camel or snake case basename of the entity
     * class, e.g. "blogPost" or "blog_post" for BlogPost.
     */
    public function matches(string $parameterName, mixed $entity): bool
    {
        if (! is_object($entity)) {
            return false;
        }

        $basename = class_basename($entity);

        return $parameterName === Str::camel($basename)
            || $parameterName === Str::snake($basename);
    }
}
