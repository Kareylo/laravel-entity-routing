<?php

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\Route;
use Kareylo\EntityRouting\EntityUrlGenerator;
use Kareylo\EntityRouting\Exceptions\EntityRouteNotFoundException;
use Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException;
use Kareylo\EntityRouting\Tests\Fixtures\Article;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;
use Kareylo\EntityRouting\Tests\Fixtures\MappedArticle;

beforeEach(function () {
    Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
    Route::get('/drafts/{id}/{slug?}', fn () => null)->name('drafts.show');
    Route::get('/blog/{category}/{year}/{slug}', fn () => null)->name('blog.show');
    Route::get('/categories/{category:slug}/articles/{article:slug}', fn () => null)->name('categories.articles.show');
    Route::getRoutes()->refreshNameLookups();

    $this->generator = $this->app->make(EntityUrlGenerator::class);
});

it('fills every placeholder from the entity', function () {
    expect($this->generator->generate('articles.show', new ArticleData))
        ->toBe('http://localhost/articles/42/my-title');
});

it('generates a relative url', function () {
    expect($this->generator->generate('articles.show', new ArticleData, absolute: false))
        ->toBe('/articles/42/my-title');
});

it('gives explicit values precedence over the entity', function () {
    expect($this->generator->generate('articles.show', new ArticleData, ['slug' => 'other'], absolute: false))
        ->toBe('/articles/42/other');
});

it('fills a parameter the entity lacks from explicit values', function () {
    expect($this->generator->generate('articles.show', new ArticleData(slug: null), ['slug' => 'other'], absolute: false))
        ->toBe('/articles/42/other');
});

it('appends explicit values that match no placeholder as query string', function () {
    expect($this->generator->generate('articles.show', new ArticleData, ['page' => 2], absolute: false))
        ->toBe('/articles/42/my-title?page=2');
});

it('uses the mapping declared by the entity before its attributes', function () {
    expect($this->generator->generate('blog.show', new MappedArticle, absolute: false))
        ->toBe('/blog/news/2026/my-title');
});

it('gives explicit values precedence over the entity mapping', function () {
    expect($this->generator->generate('blog.show', new MappedArticle, ['category' => 'tech'], absolute: false))
        ->toBe('/blog/tech/2026/my-title');
});

it('uses binding fields before attributes', function () {
    expect($this->generator->generate('categories.articles.show', new Article, absolute: false))
        ->toBe('/categories/news/articles/my-title');
});

it('omits an unresolved optional parameter', function () {
    expect($this->generator->generate('drafts.show', new ArticleData(slug: null)))
        ->toBe('http://localhost/drafts/42');
});

it('throws when required parameters cannot be resolved', function () {
    $this->generator->generate('articles.show', new ArticleData(id: null, slug: null));
})->throws(
    MissingEntityRouteParameterException::class,
    'Missing required parameters for [Route: articles.show] [URI: articles/{id}/{slug}] [Entity: '.ArticleData::class.'] [Missing parameters: id, slug].',
);

it('exposes the missing parameters on the exception', function () {
    try {
        $this->generator->generate('articles.show', new ArticleData(slug: null));
    } catch (MissingEntityRouteParameterException $e) {
        expect($e)->toBeInstanceOf(UrlGenerationException::class)
            ->and($e->routeName)->toBe('articles.show')
            ->and($e->missingParameters)->toBe(['slug'])
            ->and($e->entityType)->toBe(ArticleData::class);

        return;
    }

    $this->fail('No exception thrown.');
});

it('throws for an unknown route', function () {
    $this->generator->generate('unknown', new ArticleData);
})->throws(EntityRouteNotFoundException::class, 'Route [unknown] not defined.');
