$contribution = @'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contribution extends Model
{
    use HasFactory;
    use SoftDeletes;

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
'@
[System.IO.File]::WriteAllText("$PWD\app\Models\Contribution.php", $contribution, (New-Object System.Text.UTF8Encoding $false))

$eventItem = @'
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
'@
[System.IO.File]::WriteAllText("$PWD\app\Models\EventItem.php", $eventItem, (New-Object System.Text.UTF8Encoding $false))

Write-Host "Models updated."