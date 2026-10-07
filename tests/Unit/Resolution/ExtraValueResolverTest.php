<?php

use Kareylo\EntityRouting\Resolution\ExtraValueResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;

beforeEach(function () {
    $this->resolver = new ExtraValueResolver;
});

it('resolves the explicit value given for the parameter', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new ArticleData, ['slug' => 'other']);

    expect($resolution->resolved())->toBeTrue()
        ->and($resolution->value())->toBe('other');
});

it('does not resolve a parameter without explicit value', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new ArticleData, ['page' => 2]);

    expect($resolution->resolved())->toBeFalse();
});
