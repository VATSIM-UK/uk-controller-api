<?php

namespace App\Models\Notification;

use App\Models\Controller\ControllerPosition;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'valid_from',
        'valid_to'
    ];

    public function scopeActive($query)
    {
        return $query->where('valid_from', '<=', Carbon::now())
            ->where('valid_to', '>=', Carbon::now());
    }

    public function controllers() : BelongsToMany
    {
        return $this->belongsToMany(
            ControllerPosition::class,
            'notification_controllers',
            'notification_id',
            'controller_position_id'
        );
    }

    public function readBy() : BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'notification_reads',
            'notification_id',
            'user_id'
        );
    }
}
