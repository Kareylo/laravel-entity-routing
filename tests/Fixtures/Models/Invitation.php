<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent entity with a hidden secret attribute.
 */
class Invitation extends Model
{
    protected $guarded = [];

    protected $hidden = ['token'];
}
