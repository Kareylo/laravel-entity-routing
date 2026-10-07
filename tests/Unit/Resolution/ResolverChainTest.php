<?php

use Kareylo\EntityRouting\Resolution\ParameterResolver;
use Kareylo\EntityRouting\Resolution\Resolution;
use Kareylo\EntityRouting\Resolution\ResolverChain;
use Kareylo\EntityRouting\RouteParameter;

function fixedResolver(Resolution $resolution): ParameterResolver
{
    return new class($resolution) implements ParameterResolver
    {
        public int $calls = 0;

        public function __construct(private readonly Resolution $resolution) {}

        public function resolve(RouteParameter $parameter, mixed $entity, array $extra): Resolution
        {
            $this->calls++;

            return $this->resolution;
        }
    };
}

beforeEach(function () {
    $this->parameter = new RouteParameter('slug', false, null);
});

it('returns the first resolved value', function () {
    $first = fixedResolver(Resolution::of('first'));
    $second = fixedResolver(Resolution::of('second'));

    $resolution = (new ResolverChain([$first, $second]))->resolve($this->parameter, null, []);

    expect($resolution->value())->toBe('first')
        ->and($second->calls)->toBe(0);
});

it('falls through to the next resolver', function () {
    $chain = new ResolverChain([fixedResolver(Resolution::unresolved()), fixedResolver(Resolution::of('second'))]);

    expect($chain->resolve($this->parameter, null, [])->value())->toBe('second');
});

it('is unresolved when no resolver resolves', function () {
    $chain = new ResolverChain([fixedResolver(Resolution::unresolved())]);

    expect($chain->resolve($this->parameter, null, [])->resolved())->toBeFalse();
});
