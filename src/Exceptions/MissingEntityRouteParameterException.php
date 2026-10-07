<?php

namespace Kareylo\EntityRouting\Exceptions;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

/**
 * Thrown when required route parameters cannot be resolved from the entity.
 */
class MissingEntityRouteParameterException extends UrlGenerationException
{
    /**
     * @param  list<string>  $missingParameters
     */
    public function __construct(
        public readonly string $routeName,
        public readonly array $missingParameters,
        public readonly string $entityType,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param  list<string>  $missingParameters
     */
    public static function forEntity(Route $route, array $missingParameters, mixed $entity): self
    {
        $label = Str::plural('parameter', count($missingParameters));
        $entityType = get_debug_type($entity);

        return new self(
            (string) $route->getName(),
            $missingParameters,
            $entityType,
            sprintf(
                'Missing required %s for [Route: %s] [URI: %s] [Entity: %s] [Missing %s: %s].',
                $label,
                $route->getName(),
                $route->uri(),
                $entityType,
                $label,
                implode(', ', $missingParameters),
            ),
        );
    }
}
