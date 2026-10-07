<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Native route helper
    |--------------------------------------------------------------------------
    |
    | When enabled, Laravel's url generator is replaced by an entity aware
    | subclass, so route(), to_route(), redirect()->route() and signed
    | routes accept an entity under the reserved "_entity" parameter:
    |
    |     route('articles.show', ['_entity' => $article]);
    |
    | Calls without "_entity" keep Laravel's native behavior.
    |
    */

    'native_route_helper' => false,

];
