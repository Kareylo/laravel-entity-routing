<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Eloquent entity whose slug is an accessor computed from its title.
 */
class Post extends Model
{
    protected $guarded = [];

    /**
     * @return Attribute<string, never>
     */
    protected function slug(): Attribute
    {
        return Attribute::get(fn () => Str::slug($this->title));
    }
}
