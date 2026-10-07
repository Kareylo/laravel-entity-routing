<?php

use Kareylo\EntityRouting\EntityNaming;
use Kareylo\EntityRouting\Resolution\BindingFieldResolver;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\Tests\Fixtures\Article;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Invitation;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Team;

beforeEach(function () {
    $this->resolver = new BindingFieldResolver(new EntityNaming);
});

it('reads the binding field on the entity when the parameter is named after it', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('article', false, 'slug'), new Article, []);

    expect($resolution->value())->toBe('my-title');
});

it('reads the binding field on the related entity otherwise', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('category', false, 'slug'), new Article, []);

    expect($resolution->value())->toBe('news');
});

it('does not resolve a parameter without binding field', function () {
    $resolution = $this->resolver->resolve(new RouteParameter('slug', false, null), new Article, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve a null binding field value', function () {
    $article = new Article(category: (object) ['slug' => null]);

    $resolution = $this->resolver->resolve(new RouteParameter('category', false, 'slug'), $article, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve a hidden binding field of the entity', function () {
    $invitation = new Invitation(['id' => 1, 'token' => 'secret']);

    $resolution = $this->resolver->resolve(new RouteParameter('invitation', false, 'token'), $invitation, []);

    expect($resolution->resolved())->toBeFalse();
});

it('does not resolve a hidden binding field of a related entity', function () {
    $invitation = (new Invitation(['id' => 1]))->setRelation('team', new Team(['slug' => 'acme', 'secret' => 'hush']));

    expect($this->resolver->resolve(new RouteParameter('team', false, 'secret'), $invitation, [])->resolved())->toBeFalse()
        ->and($this->resolver->resolve(new RouteParameter('team', false, 'slug'), $invitation, [])->value())->toBe('acme');
});
