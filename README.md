# Laravel Entity Routing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kareylo/laravel-entity-routing.svg)](https://packagist.org/packages/kareylo/laravel-entity-routing)
[![Tests](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/tests.yml/badge.svg)](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/tests.yml)
[![Quality](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/quality.yml/badge.svg)](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/quality.yml)
[![Coverage](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/coverage.yml/badge.svg)](https://github.com/Kareylo/laravel-entity-routing/actions/workflows/coverage.yml)
[![PHP Version](https://img.shields.io/packagist/php-v/kareylo/laravel-entity-routing.svg)](https://packagist.org/packages/kareylo/laravel-entity-routing)
[![License](https://img.shields.io/packagist/l/kareylo/laravel-entity-routing.svg)](LICENSE)

Generate URLs by passing a whole model, array or object: every route placeholder (`{id}`, `{slug}`, `{category}`...) is filled from it.

```php
Route::get('/articles/{id}/{slug}', ShowArticle::class)->name('articles.show');

entity_route('articles.show', $article); // https://example.com/articles/42/my-title
```

## Why

Laravel fills **one** placeholder from a model: `route('posts.show', $post)` works for `{post}` or `{post:slug}`. As soon as a URL has several placeholders, every call site has to list them:

```php
route('articles.show', ['id' => $article->id, 'slug' => $article->slug]);
```

Add a `{category}` for SEO later and every one of those calls has to change.

CakePHP solves this with [entity routing](https://book.cakephp.org/5.x/development/routing.html#entity-routing): you pass the entity and the router extracts each placeholder from it. This package brings the same idea to Laravel. Change the URL structure, and no call site needs to change.

It only affects URL **generation**. Incoming requests and route model binding are untouched, nothing is stored in a database and no route is added.

## Requirements

| Laravel | PHP |
|---|---|
| 12.x | 8.2 to 8.5 |
| 13.x | 8.3 to 8.5 |

## Installation

```bash
composer require kareylo/laravel-entity-routing
```

The service provider is registered automatically through package discovery. Publishing the configuration file is only needed for the [native route helper](#native-route-helper-opt-in):

```bash
php artisan vendor:publish --tag=entity-routing-config
```

## Usage

```php
use Illuminate\Support\Facades\URL;

entity_route('articles.show', $article);                                  // https://example.com/articles/42/my-title
entity_route('articles.show', $article, ['slug' => 'other'], absolute: false); // /articles/42/other
entity_route('articles.show', $article, ['page' => 2]);                   // https://example.com/articles/42/my-title?page=2

URL::entityRoute('articles.show', $article);
url()->entityRoute('articles.show', $article);

return redirect()->toEntityRoute('articles.show', $article);
return redirect()->toEntityRoute('articles.show', $article, [], 301, ['X-Reason' => 'moved']);
```

All four share the same signature:

```php
entity_route(string $name, mixed $entity, array $extra = [], bool $absolute = true): string
```

`redirect()->toEntityRoute()` takes `$status` and `$headers` instead of `$absolute`, like `redirect()->route()`.

Values in `$extra` take precedence over the entity. Keys that match no placeholder become the query string, exactly like `route()`.

### Supported entities

Anything [`data_get()`](https://laravel.com/docs/helpers#method-data-get) can read:

- Eloquent models, including accessors
- arrays, including nested arrays
- `ArrayAccess` objects
- plain objects with public properties (or `__get` / `__isset`)

## Resolution order

For each route placeholder, the first rule that gives a non-null value wins:

| # | Rule | Example |
|---|---|---|
| 1 | Explicit value in `$extra` | `['slug' => 'other']` |
| 2 | Mapping declared by the entity (`ProvidesRouteParameters`) | `'category' => 'category.slug'` |
| 3 | Binding field | `{article:slug}` reads `$article->slug`, `{category:slug}` reads `$article->category->slug` |
| 4 | Route key, when the placeholder is named after the entity and it is `UrlRoutable` | `{article}` on `Article` uses `$article->getRouteKey()` |
| 5 | Entity attribute of the same name | `{slug}` reads `$article->slug` |
| 6 | Default set with `URL::defaults()` | `URL::defaults(['locale' => 'fr'])` |

A placeholder is "named after the entity" when it is the camel or snake case class name: `{blogPost}` or `{blog_post}` for `BlogPost`.

### Declaring a mapping

Implement `ProvidesRouteParameters` when a placeholder does not match an attribute. Values are dot notation paths, or closures receiving the entity:

```php
use Illuminate\Database\Eloquent\Model;
use Kareylo\EntityRouting\Contracts\ProvidesRouteParameters;

class Article extends Model implements ProvidesRouteParameters
{
    public function routeParameters(): array
    {
        return [
            'category' => 'category.slug',
            'year' => fn (Article $article) => $article->published_at->year,
        ];
    }
}
```

```php
Route::get('/blog/{category}/{year}/{slug}', ShowArticle::class)->name('blog.show');

entity_route('blog.show', $article); // https://example.com/blog/news/2026/my-title
```

### Optional and missing parameters

- An optional placeholder (`{slug?}`) that cannot be resolved is left out of the URL.
- A required placeholder that cannot be resolved throws `Kareylo\EntityRouting\Exceptions\MissingEntityRouteParameterException`. It extends Laravel's `UrlGenerationException`, so existing handlers still catch it, and it exposes `routeName`, `missingParameters` and `entityType`:

  ```
  Missing required parameters for [Route: articles.show] [URI: articles/{id}/{slug}] [Entity: App\Models\Article] [Missing parameters: id, slug].
  ```

- An unknown route name throws `Kareylo\EntityRouting\Exceptions\EntityRouteNotFoundException` (`Route [name] not defined.`), which extends Symfony's `RouteNotFoundException` like Laravel's own error.

Domain placeholders (`{account}.example.com`) are resolved like any other, and everything works with `php artisan route:cache`.

## Native route helper (opt-in)

Laravel's own helpers can accept an entity too, under the reserved `_entity` parameter. Enable it in `config/entity-routing.php`:

```php
'native_route_helper' => true,
```

Then:

```php
route('articles.show', ['_entity' => $article]);                                    // https://example.com/articles/42/my-title
route('articles.show', ['_entity' => $article, 'slug' => 'other', 'page' => 2], false); // /articles/42/other?page=2

to_route('articles.show', ['_entity' => $article]);
redirect()->route('articles.show', ['_entity' => $article], 301);
URL::signedRoute('articles.show', ['_entity' => $article]);
URL::temporarySignedRoute('articles.show', now()->addHour(), ['_entity' => $article]);
```

```blade
<a href="{{ route('articles.show', ['_entity' => $article]) }}">Read</a>
```

The other keys of the array behave like `$extra`. Resolution rules, exceptions and `route:cache` support are the same as with `entity_route()`.

How it works: when the flag is on, the `url` service is replaced by `Kareylo\EntityRouting\EntityAwareUrlGenerator`, a subclass of Laravel's `UrlGenerator` that keeps the original state (forced root, signing key, defaults...). Its `route()` method handles `_entity` and passes every other call to Laravel unchanged. With the flag off (the default), Laravel's generator is not touched.

Things to know:

- **`_entity` is reserved** in route parameters while the flag is on.
- **Calls without `_entity` keep Laravel's behavior**, including `route('posts.show', $post)`, which still fills placeholders by position.
- **Another package replacing the `url` service** conflicts with this one: whichever registers last wins.
- **Code holding the url generator before this package registers** keeps the original instance.
- **IDEs and static analysis** do not know the `_entity` key.

## Limitations

- **`null` means "not resolved".** A `null` value, including `['slug' => null]` in `$extra`, falls through to the next rule. An empty string is a value and is used as is.
- **Arrays have no class name.** Rule 4 never applies to them, and `{article:slug}` on an array reads `article.slug`, not the array's own `slug`. Use `{slug}` or `$extra` instead.
- **Only public data is read.** Private and protected properties of plain objects are invisible; expose them with `ProvidesRouteParameters`.
- **`route()` is unchanged by default.** Passing an entity to `route()` keeps Laravel's native behavior, unless you enable the [native route helper](#native-route-helper-opt-in) and use `_entity`.
- **Tested versions.** CI installs the lowest versions Composer allows, which are Laravel 12.69 and 13.30: older releases are blocked by security advisories. Earlier 12.x and 13.x releases are allowed by the constraints but not tested. Laravel 12.0 to 12.3 encode `%`, `?` and `#` inside values differently from later versions; this package passes values to Laravel's generator as is, so it follows whatever your version does.

## Testing

```bash
composer test            # Pest
composer test-coverage   # Pest with coverage, fails below 80%
composer analyse         # Larastan
composer format          # Pint
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT. See [LICENSE](LICENSE).
