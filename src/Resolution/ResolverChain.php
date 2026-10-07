<?php

namespace Kareylo\EntityRouting\Resolution;

use Kareylo\EntityRouting\RouteParameter;

/**
 * Tries each resolver in order and keeps the first resolved value.
 */
final class ResolverChain implements ParameterResolver
{
    /**
     * @param  list<ParameterResolver>  $resolvers
     */
    public function __construct(private readonly array $resolvers) {}

    public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
    {
        foreach ($this->resolvers as $resolver) {
            $resolution = $resolver->resolve($parameter, $entity, $extra);

            if ($resolution->resolved()) {
                return $resolution;
            }
        }

        return Resolution::unresolved();
    }
}
