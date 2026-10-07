<?php

use Kareylo\EntityRouting\EntityRoutingServiceProvider;

it('registers the service provider', function () {
    expect($this->app->getProviders(EntityRoutingServiceProvider::class))->not->toBeEmpty();
});
