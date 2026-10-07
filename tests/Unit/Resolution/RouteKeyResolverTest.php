<?php

use Kareylo\EntityRouting\EntityNaming;
use Kareylo\EntityRouting\Resolution\RouteKeyResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\Article as PlainArticle;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Article;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Page;

beforeEach(function () {
    $this->resolver = new RouteKeyResolver(new EntityNaming);
});

it('resolves the route key of an entity the parameter is named after', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('article', false, null), new Article(['id' => 42]), []);

    expect($resolution->value())->toBe(42);
});

it('uses the custom route key name of the entity', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('page', false, null), new Page(['id' => 7, 'slug' => 'about']), []);

    expect($resolution->value())->toBe('about');
});

it('does not resolve a parameter named after something else', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('post', false, null), new Article(['id' => 42]), []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve an entity that is not url routable', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('article', false, null), new PlainArticle, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve a missing route key', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('article', false, null), new Article, []);

    expect($resolution->resolved())->toBeFalse();
});
