<?php

namespace App\Models\User;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model implements \Illuminate\Contracts\Auth\Authenticatable, FilamentUser
{
    use Authenticatable;

    public $timestamps = true;

    public $incrementing = false;

    public $table = 'admin';

    public $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->user->id);
    }

    public function canAccessFilament(): bool
    {
        return true;
    }
}
