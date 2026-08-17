<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a user of the plugin.
 *
 * Class User
 */
class UserStatus extends Model
{
    /**
     * Values for the different user statuses.
     */
    public const ACTIVE = 1;

    public const BANNED = 2;

    public const DISABLED = 3;

    public const STATUS_MESSAGES = [
        self::ACTIVE => 'Active',
        self::BANNED => 'Banned',
        self::DISABLED => 'Disabled',
    ];

    // The table name
    protected $table = 'user_status';

    protected function getActiveAttribute()
    {
        return $this->id === self::ACTIVE;
    }

    protected function getBannedAttribute()
    {
        return $this->id === self::BANNED;
    }

    protected function getDisabledAttribute()
    {
        return $this->id === self::DISABLED;
    }

    public function statusMessage(): string
    {
        return self::STATUS_MESSAGES[$this->id];
    }
}
