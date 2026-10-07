<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent entity routed by slug instead of primary key.
 */
class Page extends Model
{
    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
