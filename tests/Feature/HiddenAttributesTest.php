<?php

use Illuminate\Support\Facades\Route;
use Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Invitation;
use Kareylo\EntityRouting\Tests\Fixtures\Models\MappedInvitation;

beforeEach(function () {
    Route::get('/invitations/{id}/{token}', fn () => null)->name('invitations.accept');
    Route::getRoutes()->refreshNameLookups();
});

it('does not put hidden attributes in urls implicitly', function () {
    entity_route('invitations.accept', new Invitation(['id' => 1, 'token' => 'secret']));
})->throws(MissingEntityRouteParameterException::class, '[Missing parameter: token]');

it('uses a hidden attribute given explicitly', function () {
    $invitation = new Invitation(['id' => 1, 'token' => 'secret']);

    expect(entity_route('invitations.accept', $invitation, ['token' => $invitation->token], absolute: false))
        ->toBe('/invitations/1/secret');
});

it('uses a hidden attribute declared in the entity mapping', function () {
    expect(entity_route('invitations.accept', new MappedInvitation(['id' => 1, 'token' => 'secret']), absolute: false))
        ->toBe('/invitations/1/secret');
});
