<?php

namespace Kareylo\EntityRouting\Tests\Fixtures;

/**
 * Plain object entity exposing public properties.
 */
class ArticleData
{
    public function __construct(
        public ?int $id = 42,
        public ?string $slug = 'my-title',
    ) {}
}
