<?php

namespace App\Models\Notification;

use App\Models\Controller\ControllerPosition;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'body',
        'link',
        'valid_from',
        'valid_to'
    ];

    public function toArray(): array
    {
        $this->setHidden(['created_at', 'updated_at']);
        return array_merge(
            parent::toArray(),
            [
                'controllers' => $this->controllers->pluck('callsign')->toArray(),
            ]
        );
    }

    public function scopeActive($query)
    {
        return $query->where('valid_from', '<=', Carbon::now())
            ->where('valid_to', '>=', Carbon::now());
    }

    public function scopeUnreadBy($query, User $user)
    {
        return $query->whereDoesntHave('readBy', function ($userQuery) use ($user) {
            return $userQuery->where('user.id', $user->id);
        });
    }

    public function controllers() : BelongsToMany
    {
        return $this->belongsToMany(
            ControllerPosition::class,
            'controller_position_notification',
            'notification_id',
            'controller_position_id'
        );
    }

    public function readBy() : BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'notification_user',
            'notification_id',
            'user_id'
        );
    }

    public function getActiveAttribute() : bool
    {
        return $this->valid_to >= Carbon::now();
    }
}
