<?php

namespace Kareylo\EntityRouting\Resolution;

/**
 * Outcome of resolving one route parameter: a value, or nothing.
 */
final class Resolution
{
    private function __construct(
        private readonly bool $resolved,
        private readonly mixed $value,
    ) {}

    /**
     * A null value cannot be placed in a url, so it counts as unresolved.
     */
    public static function of(mixed $value): self
    {
        return $value === null ? self::unresolved() : new self(true, $value);
    }

    public static function unresolved(): self
    {
        return new self(false, null);
    }

    public function resolved(): bool
    {
        return $this->resolved;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
