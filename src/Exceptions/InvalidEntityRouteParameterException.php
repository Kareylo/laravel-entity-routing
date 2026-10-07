<?php

namespace Kareylo\EntityRouting\Exceptions;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;

/**
 * Thrown when a resolved value cannot be placed in the route safely.
 */
class InvalidEntityRouteParameterException extends UrlGenerationException
{
    public function __construct(
        public readonly string $routeName,
        public readonly string $parameterName,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function forDomain(Route $route, string $parameterName): self
    {
        $routeName = (string) $route->getName();

        return new self(
            $routeName,
            $parameterName,
            "Invalid value for domain parameter [{$parameterName}] of route [{$routeName}].",
        );
    }
}
