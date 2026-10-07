<?php

namespace Kareylo\EntityRouting\Concerns;

use Kareylo\EntityRouting\EntityUrlGenerator;

/**
 * Exposes the resolved urls of an Eloquent model as the "entity_urls"
 * attribute, appended when the model is serialized.
 */
trait HasEntityUrls
{
    /**
     * Map a key of "entity_urls" to the name of the route to resolve.
     *
     * @return array<string, string>
     */
    abstract public function entityRoutes(): array;

    public function initializeHasEntityUrls(): void
    {
        $this->append('entity_urls');
    }

    /**
     * @return array<string, string>
     */
    public function getEntityUrlsAttribute(): array
    {
        $urls = app(EntityUrlGenerator::class);

        return array_map(fn (string $route) => $urls->generate($route, $this), $this->entityRoutes());
    }
}
