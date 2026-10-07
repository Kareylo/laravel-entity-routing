<?php

use Illuminate\Routing\Router;
use Kareylo\EntityRouting\RouteParameter;
use Kareylo\EntityRouting\RouteParameterReader;

beforeEach(function () {
    $this->router = $this->app->make(Router::class);
    $this->reader = new RouteParameterReader;
});

it('reads required parameters in uri order', function () {
    $route = $this->router->get('/articles/{id}/{slug}', fn () => null);

    expect($this->reader->read($route))->toEqual([
        new RouteParameter('id', optional: false, bindingField: null),
        new RouteParameter('slug', optional: false, bindingField: null),
    ]);
});

it('flags optional parameters', function () {
    $route = $this->router->get('/articles/{id}/{slug?}', fn () => null);

    expect($this->reader->read($route))->toEqual([
        new RouteParameter('id', optional: false, bindingField: null),
        new RouteParameter('slug', optional: true, bindingField: null),
    ]);
});

it('reads binding fields', function () {
    $route = $this->router->get('/categories/{category:slug}/articles/{article}', fn () => null);

    expect($this->reader->read($route))->toEqual([
        new RouteParameter('category', optional: false, bindingField: 'slug'),
        new RouteParameter('article', optional: false, bindingField: null),
    ]);
});

it('reads optional parameters with a binding field', function () {
    $route = $this->router->get('/articles/{article:slug?}', fn () => null);

    expect($this->reader->read($route))->toEqual([
        new RouteParameter('article', optional: true, bindingField: 'slug'),
    ]);
});

it('reads domain parameters before uri parameters', function () {
    $route = $this->router->get('/users/{user}', fn () => null)->domain('{account}.example.com');

    expect($this->reader->read($route))->toEqual([
        new RouteParameter('account', optional: false, bindingField: null, inDomain: true),
        new RouteParameter('user', optional: false, bindingField: null),
    ]);
});

it('returns no parameters for a static route', function () {
    $route = $this->router->get('/about', fn () => null);

    expect($this->reader->read($route))->toBe([]);
});
