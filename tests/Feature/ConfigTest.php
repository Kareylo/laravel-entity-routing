<?php

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Kareylo\EntityRouting\EntityRoutingServiceProvider;

it('disables the native route helper by default', function () {
    expect(config('entity-routing.native_route_helper'))->toBeFalse();
});

it('publishes the config file', function () {
    $paths = ServiceProvider::pathsToPublish(EntityRoutingServiceProvider::class, 'entity-routing-config');

    expect($paths)->toHaveCount(1)
        ->and(array_key_first($paths))->toEndWith('config/entity-routing.php')
        ->and(array_values($paths)[0])->toBe(config_path('entity-routing.php'));
});

it('keeps the native url generator when the native route helper is disabled', function () {
    expect(get_class(app('url')))->toBe(UrlGenerator::class);
});
