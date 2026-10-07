<?php

use Illuminate\Support\Facades\URL;
use Kareylo\EntityRouting\EntityAwareUrlGenerator;

it('replaces the url generator when the native route helper is enabled', function () {
    expect(app('url'))->toBeInstanceOf(EntityAwareUrlGenerator::class)
        ->and(URL::getFacadeRoot())->toBe(app('url'));
});

it('gives the redirector the replaced url generator', function () {
    expect(app('redirect')->getUrlGenerator())->toBe(app('url'));
});
