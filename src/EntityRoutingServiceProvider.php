<?php

namespace Kareylo\EntityRouting;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\Resolution\BindingFieldResolver;
use Kareylo\EntityRouting\Resolution\EntityMappingResolver;
use Kareylo\EntityRouting\Resolution\ExtraValueResolver;
use Kareylo\EntityRouting\Resolution\ParameterResolver;
use Kareylo\EntityRouting\Resolution\ResolverChain;
use Kareylo\EntityRouting\Resolution\RouteKeyResolver;

class EntityRoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ParameterResolver::class, fn (Container $app) => new ResolverChain([
            new ExtraValueResolver,
            new EntityMappingResolver,
            new BindingFieldResolver($app->make(EntityNaming::class)),
            new RouteKeyResolver($app->make(EntityNaming::class)),
            new AttributeResolver,
        ]));
    }
}
