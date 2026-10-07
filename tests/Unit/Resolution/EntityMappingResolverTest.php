<?php

use Kareylo\EntityRouting\Resolution\EntityMappingResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;
use Kareylo\EntityRouting\Tests\Fixtures\MappedArticle;

beforeEach(function () {
    $this->resolver = new EntityMappingResolver;
});

it('resolves a dot notation path declared by the entity', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('category', false, null), new MappedArticle, []);

    expect($resolution->value())->toBe('news');
});

it('resolves a closure declared by the entity', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('year', false, null), new MappedArticle, []);

    expect($resolution->value())->toBe(2026);
});

it('does not resolve a parameter absent from the mapping', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new MappedArticle, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve when the mapping gives null', function () {
    $article = new MappedArticle(category: (object) ['slug' => null]);

    $resolution = $this->resolver->resolve(new RouteParameter('category', false, null), $article, []);

    expect($resolution->resolved())->toBeFalse();
});

it('ignores entities without a mapping', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new ArticleData, []);

    expect($resolution->resolved())->toBeFalse();
});
