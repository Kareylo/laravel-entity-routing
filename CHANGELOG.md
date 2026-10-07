# Changelog

All notable changes to this project are documented in this file.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [0.1.0] - 2026-10-07

### Added

- `entity_route()` helper, `URL::entityRoute()` / `url()->entityRoute()` and `redirect()->toEntityRoute()` macros: generate a route url by filling every placeholder from one entity.
- Resolution rules, in order: explicit values, `ProvidesRouteParameters` mapping (dot notation or closure), binding fields (`{article:slug}`, `{category:slug}`), route key of a `UrlRoutable` entity, attribute of the same name, `URL::defaults()`.
- Entities: Eloquent models (attributes and accessors), arrays, `ArrayAccess`, plain objects.
- Optional placeholders left out when unresolved; `MissingEntityRouteParameterException` and `EntityRouteNotFoundException`.
- Domain placeholders and `route:cache` support.
- Supports Laravel 12 and 13 on PHP 8.2 to 8.5.
- CI: test matrix, Larastan, Pint, coverage (minimum 80%) and GitHub Release on tags.

[Unreleased]: https://github.com/Kareylo/laravel-entity-routing/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/Kareylo/laravel-entity-routing/releases/tag/v0.1.0
