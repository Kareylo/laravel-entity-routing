<?php

use Illuminate\Routing\CompiledRouteCollection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Kareylo\EntityRouting\Tests\Fixtures\Article;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;

describe('domain routes', function () {
    beforeEach(function () {
        Route::domain('{account}.example.com')->get('/users/{user}', fn () => null)->name('users.show');
        Route::getRoutes()->refreshNameLookups();
    });

    it('fills domain placeholders from the entity', function () {
        expect(entity_route('users.show', ['account' => 'acme', 'user' => 5]))
            ->toBe('http://acme.example.com/users/5');
    });

    it('fills domain placeholders from explicit values', function () {
        expect(entity_route('users.show', ['user' => 5], ['account' => 'acme']))
            ->toBe('http://acme.example.com/users/5');
    });
});

describe('url defaults', function () {
    beforeEach(function () {
        Route::get('/{locale}/articles/{id}', fn () => null)->name('localized.show');
        Route::getRoutes()->refreshNameLookups();
        URL::defaults(['locale' => 'fr']);
    });

    it('falls back to url defaults for parameters the entity lacks', function () {
        expect(entity_route('localized.show', new ArticleData, absolute: false))
            ->toBe('/fr/articles/42');
    });

    it('prefers the entity value over url defaults', function () {
        expect(entity_route('localized.show', ['locale' => 'en', 'id' => 42], absolute: false))
            ->toBe('/en/articles/42');
    });
});

describe('encoding', function () {
    beforeEach(function () {
        Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
        Route::getRoutes()->refreshNameLookups();
    });

    it('encodes values exactly like route() does', function () {
        $slug = 'a b?c#d%e/f';

        expect(entity_route('articles.show', new ArticleData(slug: $slug)))
            ->toBe(route('articles.show', ['id' => 42, 'slug' => $slug]));
    });
});

describe('route cache', function () {
    it('generates urls from cached routes', function () {
        $this->defineCacheRoutes(<<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/categories/{category:slug}/articles/{article:slug?}', fn () => null)->name('cached.articles.show');
Route::domain('{account}.example.com')->get('/users/{user}', fn () => null)->name('cached.users.show');
PHP);

        expect(app('router')->getRoutes())->toBeInstanceOf(CompiledRouteCollection::class)
            ->and(entity_route('cached.articles.show', new Article))
            ->toBe('http://localhost/categories/news/articles/my-title')
            ->and(entity_route('cached.articles.show', new Article(slug: null)))
            ->toBe('http://localhost/categories/news/articles')
            ->and(entity_route('cached.users.show', ['account' => 'acme', 'user' => 5]))
            ->toBe('http://acme.example.com/users/5');
    });
});
