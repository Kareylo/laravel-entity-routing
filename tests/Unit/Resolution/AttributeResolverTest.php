<?php

use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;

beforeEach(function () {
    $this->resolver = new AttributeResolver;
});

it('resolves the attribute named after the parameter', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new ArticleData, []);

    expect($resolution->resolved())->toBeTrue()
        ->and($resolution->value())->toBe('my-title');
});

it('does not resolve a missing attribute', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('category', false, null), new ArticleData, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve a null attribute', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new ArticleData(slug: null), []);

    expect($resolution->resolved())->toBeFalse();
});
