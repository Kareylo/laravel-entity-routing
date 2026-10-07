<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\CompiledRouteCollection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException;
use Kareylo\EntityRouting\Tests\Fixtures\Article;
use Kareylo\EntityRouting\Tests\Fixtures\ArticleData;
use Kareylo\EntityRouting\Tests\Fixtures\Models\Article as ArticleModel;

beforeEach(function () {
    Route::get('/articles/{id}/{slug}', fn () => null)->name('articles.show');
    Route::get('/{locale}/articles/{id}', fn () => null)->name('localized.show');
    Route::get('/posts/{article}', fn () => null)->name('posts.show');
    Route::getRoutes()->refreshNameLookups();
});

it('fills parameters from _entity with the route() helper', function () {
    expect(route('articles.show', ['_entity' => new ArticleData]))
        ->toBe('http://localhost/articles/42/my-title');
});

it('passes explicit values and the absolute flag through the route() helper', function () {
    expect(route('articles.show', ['_entity' => new ArticleData, 'slug' => 'other', 'page' => 2], false))
        ->toBe('/articles/42/other?page=2');
});

it('keeps native route() calls unchanged', function () {
    expect(route('posts.show', new ArticleModel(['id' => 42])))->toBe('http://localhost/posts/42')
        ->and(route('articles.show', ['id' => 7, 'slug' => 'native'], false))->toBe('/articles/7/native');
});

it('redirects with to_route()', function () {
    $response = to_route('articles.show', ['_entity' => new ArticleData]);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe('http://localhost/articles/42/my-title');
});

it('redirects with redirect()->route()', function () {
    $response = redirect()->route('articles.show', ['_entity' => new ArticleData], 301);

    expect($response->getTargetUrl())->toBe('http://localhost/articles/42/my-title')
        ->and($response->getStatusCode())->toBe(301);
});

it('signs urls generated from _entity', function () {
    $url = URL::signedRoute('articles.show', ['_entity' => new ArticleData]);

    expect($url)->toStartWith('http://localhost/articles/42/my-title?signature=')
        ->and(URL::hasValidSignature(Request::create($url)))->toBeTrue();
});

it('signs temporary urls generated from _entity', function () {
    $url = URL::temporarySignedRoute('articles.show', now()->addMinutes(5), ['_entity' => new ArticleData]);

    expect($url)->toStartWith('http://localhost/articles/42/my-title?expires=')
        ->and(URL::hasValidSignature(Request::create($url)))->toBeTrue();
});

it('fills parameters from _entity in Blade', function () {
    $html = Blade::render("{{ route('articles.show', ['_entity' => \$article]) }}", ['article' => new ArticleData]);

    expect($html)->toBe('http://localhost/articles/42/my-title');
});

it('falls back to url defaults set after boot', function () {
    URL::defaults(['locale' => 'fr']);

    expect(route('localized.show', ['_entity' => new ArticleData], false))->toBe('/fr/articles/42');
});

it('throws when the entity lacks required parameters', function () {
    route('articles.show', ['_entity' => new ArticleData(slug: null)]);
})->throws(MissingEntityRouteParameterException::class);

it('fills parameters from _entity with cached routes', function () {
    $this->defineCacheRoutes(<<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/categories/{category:slug}/articles/{article:slug}', fn () => null)->name('cached.articles.show');
PHP);

    expect(app('router')->getRoutes())->toBeInstanceOf(CompiledRouteCollection::class)
        ->and(route('cached.articles.show', ['_entity' => new Article]))
        ->toBe('http://localhost/categories/news/articles/my-title');
});
