<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kareylo\EntityRouting\EntityAwareUrlGenerator;
use Kareylo\EntityRouting\EntityUrlGenerator;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;

beforeEach(function () {
    Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
    Route::get('/{locale}/articles/{id}', fn () => null)->name('localized.show');
    Route::getRoutes()->refreshNameLookups();

    $this->original = app('url');
    $this->entities = fn () => app(EntityUrlGenerator::class);
});

it('keeps the forced root, defaults and key resolver of the original generator', function () {
    $this->original->forceRootUrl('https://example.test');
    $this->original->defaults(['locale' => 'fr']);
    $this->original->setKeyResolver(fn () => 'secret-key');

    $copy = EntityAwareUrlGenerator::fromGenerator($this->original, $this->entities);
    $signed = $copy->signedRoute('localized.show', ['id' => 42]);

    expect($copy->route('localized.show', ['id' => 42]))->toBe($this->original->route('localized.show', ['id' => 42]))
        ->and($copy->route('localized.show', ['id' => 42]))->toBe('http://example.test/fr/articles/42')
        ->and($copy->getDefaultParameters())->toBe(['locale' => 'fr'])
        ->and($copy->hasValidSignature(Request::create($signed)))->toBeTrue();
});

it('does not share its route generator with the original generator', function () {
    $this->original->defaults(['locale' => 'fr']);
    $this->original->route('localized.show', ['id' => 42]);

    $copy = EntityAwareUrlGenerator::fromGenerator($this->original, $this->entities);
    $copy->defaults(['locale' => 'en']);

    expect($copy->route('localized.show', ['id' => 42], false))->toBe('/en/articles/42')
        ->and($this->original->route('localized.show', ['id' => 42], false))->toBe('/fr/articles/42');
});

it('fills parameters from the entity given under _entity', function () {
    $copy = EntityAwareUrlGenerator::fromGenerator($this->original, $this->entities);

    expect($copy->route('articles.show', ['_entity' => new ArticleData, 'page' => 2], false))
        ->toBe('/articles/42/my-title?page=2');
});

it('behaves natively without _entity', function () {
    $copy = EntityAwareUrlGenerator::fromGenerator($this->original, $this->entities);

    expect($copy->route('articles.show', ['id' => 7, 'slug' => 'native'], false))
        ->toBe($this->original->route('articles.show', ['id' => 7, 'slug' => 'native'], false))
        ->and($copy->route('articles.show', [7, 'native'], false))
        ->toBe('/articles/7/native');
});

it('only accepts an object under _entity', function () {
    $copy = EntityAwareUrlGenerator::fromGenerator($this->original, $this->entities);

    $copy->route('articles.show', ['_entity' => ['id' => 42, 'slug' => 'my-title']]);
})->throws(InvalidArgumentException::class, 'The "_entity" route parameter must be an object.');
