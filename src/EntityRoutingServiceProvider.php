<?php

namespace Kareylo\EntityRouting;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\Resolution\BindingFieldResolver;
use Kareylo\EntityRouting\Resolution\EntityMappingResolver;
use Kareylo\EntityRouting\Resolution\ExtraValueResolver;
use Kareylo\EntityRouting\Resolution\ParameterResolver;
use Kareylo\EntityRouting\Resolution\ResolverChain;
use Kareylo\EntityRouting\Resolution\RouteKeyResolver;
use Kareylo\EntityRouting\Resolution\UrlDefaultsResolver;

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
            new UrlDefaultsResolver($app->make(UrlGenerator::class)),
        ]));
    }

    public function boot(): void
    {
        UrlGenerator::macro('entityRoute', function (string $name, mixed $entity, array $extra = [], bool $absolute = true): string {
            return app(EntityUrlGenerator::class)->generate($name, $entity, $extra, $absolute);
        });

        Redirector::macro('toEntityRoute', function (string $name, mixed $entity, array $extra = [], int $status = 302, array $headers = []): RedirectResponse {
            /** @var Redirector $this */
            return $this->to(app(EntityUrlGenerator::class)->generate($name, $entity, $extra), $status, $headers);
        });
    }
}
