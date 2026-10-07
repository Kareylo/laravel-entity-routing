<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Kareylo\EntityRouting\Contracts\ProvidesRouteParameters;

/**
 * Invitation explicitly exposing its hidden token to routing.
 */
class MappedInvitation extends Invitation implements ProvidesRouteParameters
{
    public function routeParameters(): array
    {
        return ['token' => 'token'];
    }
}
