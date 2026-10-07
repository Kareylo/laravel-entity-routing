<?php

namespace Kareylo\EntityRouting\Tests\Fixtures;

/**
 * Plain object entity with a related category.
 */
class Article
{
    public function __construct(
        public ?string $slug = 'my-title',
        public ?object $category = null,
    ) {
        $this->category ??= (object) ['slug' => 'news'];
    }
}
