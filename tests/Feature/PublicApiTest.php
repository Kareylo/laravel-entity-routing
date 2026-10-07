<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;

beforeEach(function () {
    Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
    Route::getRoutes()->refreshNameLookups();
});

it('generates an absolute url with the helper', function () {
    expect(entity_route('articles.show', new ArticleData))
        ->toBe('http://localhost/articles/42/my-title');
});

it('passes explicit values and the absolute flag through the helper', function () {
    expect(entity_route('articles.show', new ArticleData, ['slug' => 'other'], absolute: false))
        ->toBe('/articles/42/other');
});

it('generates a url with the URL facade macro', function () {
    expect(URL::entityRoute('articles.show', new ArticleData, ['page' => 2], absolute: false))
        ->toBe('/articles/42/my-title?page=2');
});

it('generates a url with the url() helper macro', function () {
    expect(url()->entityRoute('articles.show', new ArticleData))
        ->toBe('http://localhost/articles/42/my-title');
});

it('redirects to the entity route', function () {
    $response = redirect()->toEntityRoute('articles.show', new ArticleData);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe('http://localhost/articles/42/my-title')
        ->and($response->getStatusCode())->toBe(302);
});

it('redirects with explicit values, status and headers', function () {
    $response = redirect()->toEntityRoute('articles.show', new ArticleData, ['slug' => 'other'], 301, ['X-Test' => 'yes']);

    expect($response->getTargetUrl())->toBe('http://localhost/articles/42/other')
        ->and($response->getStatusCode())->toBe(301)
        ->and($response->headers->get('X-Test'))->toBe('yes');
});
