<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'type', 'description', 'address', 'created_by', 'status', 'bank_code', 'bank_name', 'account_number', 'account_name', 'paystack_subaccount_code'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $base = Str::slug($organization->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i;
                    $i++;
                }
                $organization->slug = $slug;
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function events()
    {
        return $this->hasMany(EventItem::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function hasPayoutSetup(): bool
    {
        return ! empty($this->paystack_subaccount_code);
    }
}
