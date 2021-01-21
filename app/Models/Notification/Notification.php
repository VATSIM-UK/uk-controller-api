<?php

namespace App\Models\Notification;

use App\Models\Controller\ControllerPosition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'valid_from',
        'valid_to',
        'disabled_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('valid_from', '<=', Carbon::now())
            ->where('valid_to', '>=', Carbon::now())
            ->whereNull('disabled_at');
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
}
