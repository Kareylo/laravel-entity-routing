<?php

namespace Kareylo\EntityRouting;

use Illuminate\Support\ServiceProvider;
use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\Resolution\EntityMappingResolver;
use Kareylo\EntityRouting\Resolution\ExtraValueResolver;
use Kareylo\EntityRouting\Resolution\ParameterResolver;
use Kareylo\EntityRouting\Resolution\ResolverChain;

class EntityRoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ParameterResolver::class, fn () => new ResolverChain([
            new ExtraValueResolver,
            new EntityMappingResolver,
            new AttributeResolver,
        ]));
    }
}
