# Contributing

Thanks for helping. Bug reports, ideas and pull requests are welcome.

## Before you start

- **Security issues:** do not open a public issue. Follow [SECURITY.md](SECURITY.md).
- **Bugs:** open an issue with the route definition, the entity (model, array or object), the call you made, and the url you expected versus the one you got. Include your Laravel and PHP versions.
- **Features:** open an issue first to discuss it, so no work is wasted on something that does not fit the package.
- **New dependencies:** the package only requires `php` and `illuminate/*` components. Any new runtime or dev dependency must be discussed in an issue first.

## Setup

```bash
git clone https://github.com/Kareylo/laravel-entity-routing.git
cd laravel-entity-routing
composer install
```

Requirements: PHP 8.2+ and a coverage driver (PCOV or Xdebug) for `composer test-coverage`.

## Workflow

1. Create a branch named after the change type: `feat/...`, `fix/...`, `docs/...`, `test/...`, `refactor/...`, `chore/...`.
2. Write a failing test first, then the minimal code that makes it pass, then refactor with the tests green. No production code without a test requiring it.
3. Run every check before pushing:
   ```bash
   composer test-coverage   # Pest with coverage, fails below 80% (the project targets 100%)
   composer analyse         # Larastan, level max
   composer format          # Pint, then commit the formatting
   ```
4. Add a line under `## [Unreleased]` in [CHANGELOG.md](CHANGELOG.md) for any user-facing change.
5. Open a pull request against `main`. It is squash merged once the `tests passed`, `quality` and `coverage` checks are green.

## Conventions

- **Commit and pull request titles:** lowercase type, colon, space, then the summary: `feat: resolve route keys of related entities`. Types: `feat`, `fix`, `update`, `docs`, `test`, `refactor`, `chore`.
- **Supported versions:** Laravel 12 and 13, PHP 8.2 to 8.5. A change must work identically on all of them; CI runs the full matrix with lowest and stable dependencies.
- **Design:** one responsibility per class. A new resolution rule is a new `ParameterResolver` registered in the chain, without changing the existing rules.
- **Language:** code, PHPDoc, exception messages and documentation in English.

## License

By contributing, you agree that your contributions are licensed under the [MIT License](LICENSE).
