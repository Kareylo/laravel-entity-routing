<?php

use Illuminate\Support\Facades\Route;
use Kareylo\EntityRouting\EntityUrlGenerator;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Article;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Post;

beforeEach(function () {
    Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
    Route::get('/categories/{category:slug}/articles/{slug}', fn () => null)->name('categories.articles.show');
    Route::getRoutes()->refreshNameLookups();

    $this->generator = $this->app->make(EntityUrlGenerator::class);
});

it('reads Eloquent attributes', function () {
    expect($this->generator->generate('articles.show', new Article(['id' => 42, 'slug' => 'my-title']), absolute: false))
        ->toBe('/articles/42/my-title');
});

it('reads Eloquent accessors', function () {
    expect($this->generator->generate('articles.show', new Post(['id' => 42, 'title' => 'My Title']), absolute: false))
        ->toBe('/articles/42/my-title');
});

it('reads array keys', function () {
    expect($this->generator->generate('articles.show', ['id' => 42, 'slug' => 'my-title'], absolute: false))
        ->toBe('/articles/42/my-title');
});

it('reads nested array keys through binding fields', function () {
    $entity = ['slug' => 'my-title', 'category' => ['slug' => 'news']];

    expect($this->generator->generate('categories.articles.show', $entity, absolute: false))
        ->toBe('/categories/news/articles/my-title');
});

it('reads ArrayAccess offsets', function () {
    expect($this->generator->generate('articles.show', new ArrayObject(['id' => 42, 'slug' => 'my-title']), absolute: false))
        ->toBe('/articles/42/my-title');
});

it('reads public properties of plain objects', function () {
    expect($this->generator->generate('articles.show', new ArticleData, absolute: false))
        ->toBe('/articles/42/my-title');
});

it('reads properties of anonymous objects', function () {
    expect($this->generator->generate('articles.show', (object) ['id' => 42, 'slug' => 'my-title'], absolute: false))
        ->toBe('/articles/42/my-title');
});
