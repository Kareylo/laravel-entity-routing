<?php

namespace Kareylo\EntityRouting\Tests\Fixtures;

use Carbon\CarbonImmutable;
use Kareylo\EntityRouting\Contracts\ProvidesRouteParameters;

/**
 * Plain object entity declaring its own route parameter mapping.
 */
class MappedArticle implements ProvidesRouteParameters
{
    public CarbonImmutable $published_at;

    public function __construct(
        public ?object $category = null,
        public string $slug = 'my-title',
    ) {
        $this->category ??= (object) ['slug' => 'news'];
        $this->published_at = CarbonImmutable::parse('2026-10-07');
    }

    public function routeParameters(): array
    {
        return [
            'category' => 'category.slug',
            'year' => fn (self $article) => $article->published_at->year,
        ];
    }
}
