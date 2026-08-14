<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'body', 'is_pinned', 'published_at', 'organization_id', 'type'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
