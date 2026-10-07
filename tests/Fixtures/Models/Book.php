<?php

namespace Kareylo\EntityRouting\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Kareylo\EntityRouting\Concerns\HasEntityUrls;

/**
 * Eloquent entity exposing its resolved urls.
 */
class Book extends Model
{
    use HasEntityUrls;

    protected $guarded = [];

    public function entityRoutes(): array
    {
        return [
            'show' => 'books.show',
            'edit' => 'books.edit',
        ];
    }
}
