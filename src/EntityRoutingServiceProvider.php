<?php

namespace Kareylo\EntityRouting;

use Illuminate\Support\ServiceProvider;
use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\Resolution\ParameterResolver;

class EntityRoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ParameterResolver::class, AttributeResolver::class);
    }
}
