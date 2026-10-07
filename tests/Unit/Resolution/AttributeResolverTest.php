<?php

use Kareylo\EntityRouting\Resolution\AttributeResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Article;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Invitation;

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

it('does not resolve a hidden Eloquent attribute', function () {
    $invitation = new Invitation(['id' => 1, 'token' => 'secret']);

    $resolution = $this->resolver->resolve(new RouteParameter('token', false, null), $invitation, []);

    expect($resolution->resolved())->toBeFalse();
});

it('resolves a hidden Eloquent attribute made visible', function () {
    $invitation = (new Invitation(['id' => 1, 'token' => 'secret']))->makeVisible('token');

    $resolution = $this->resolver->resolve(new RouteParameter('token', false, null), $invitation, []);

    expect($resolution->value())->toBe('secret');
});

it('does not resolve an attribute outside the visible list of an Eloquent model', function () {
    $article = (new Article(['id' => 1, 'slug' => 'my-title']))->setVisible(['id']);

    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), $article, []);

    expect($resolution->resolved())->toBeFalse();
});
