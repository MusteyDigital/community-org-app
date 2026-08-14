<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'event_date', 'event_time', 'location', 'created_by', 'organization_id'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}