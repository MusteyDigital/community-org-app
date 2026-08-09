<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'body', 'is_pinned', 'published_at', 'organization_id', 'type'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
