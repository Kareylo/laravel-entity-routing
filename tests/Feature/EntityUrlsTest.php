<?php

use Illuminate\Support\Facades\Route;
use Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Book;

beforeEach(function () {
    Route::get('/books/{book}/{slug}', fn () => null)->name('books.show');
    Route::get('/books/{book}/edit', fn () => null)->name('books.edit');
    Route::getRoutes()->refreshNameLookups();

    $this->book = new Book(['id' => 42, 'slug' => 'my-title']);
    $this->book->exists = true;
});

it('resolves every declared route of the model', function () {
    expect($this->book->entity_urls)->toBe([
        'show' => 'http://localhost/books/42/my-title',
        'edit' => 'http://localhost/books/42/edit',
    ]);
});

it('adds the resolved urls when the model is serialized', function () {
    expect($this->book->toArray()['entity_urls'])->toBe([
        'show' => 'http://localhost/books/42/my-title',
        'edit' => 'http://localhost/books/42/edit',
    ]);
});

it('adds the resolved urls to json, as sent in Inertia props', function () {
    $json = json_decode(collect([$this->book])->toJson(), true);

    expect($json[0]['entity_urls']['show'])->toBe('http://localhost/books/42/my-title');
});

it('can leave the urls out of serialization', function () {
    expect($this->book->makeHidden('entity_urls')->toArray())->not->toHaveKey('entity_urls');
});

it('gives no urls for a model that is not saved yet', function () {
    expect((new Book(['slug' => 'my-title']))->toArray()['entity_urls'])->toBe([]);
});

it('throws when a declared route of a saved model cannot be resolved', function () {
    $book = new Book(['slug' => 'my-title']);
    $book->exists = true;

    $book->toArray();
})->throws(MissingEntityRouteParameterException::class);
