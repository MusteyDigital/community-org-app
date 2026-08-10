<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = ['organization_id', 'member_id', 'amount', 'category', 'note', 'contributed_at', 'payment_reference', 'source', 'payment_status'];

    protected function casts(): array
    {
        return [
            'contributed_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
