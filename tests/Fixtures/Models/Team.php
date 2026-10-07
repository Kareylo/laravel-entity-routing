<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Related Eloquent entity with a hidden secret attribute.
 */
class Team extends Model
{
    protected $guarded = [];

    protected $hidden = ['secret'];
}
