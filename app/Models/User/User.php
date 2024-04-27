<?php

namespace App\Models\User;

use App\Models\Dependency\Dependency;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Model for a user of the plugin.
 *
 * Class User
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, FilamentUser, HasName
{
    use HasApiTokens, Authenticatable, Authorizable, HasFactory, LogsActivity;

    // The table name
    protected $table = 'user';

    // The user IDs are VATSIM CIDs
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'last_login',
        'send_stand_acars_messages',
        'stand_acars_messages_uncontrolled_airfield',
    ];
    
    protected $hidden = [
        'last_login_ip',
    ];

    protected $casts = [
        'send_stand_acars_messages' => 'boolean',
    ];

    /**
     * Returns the relation to the users status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function accountStatus(): HasOne
    {
        return $this->hasOne(UserStatus::class, 'id', 'status');
    }

    /**
     * Sets the last login time of the user
     *
     * @return User
     */
    public function touchLastLogin(): User
    {
        $this->last_login = Carbon::now();
        $this->save();
        return $this;
    }

    /**
     * Marks the user as banned
     *
     * @return User
     */
    public function ban(): User
    {
        $this->status = UserStatus::BANNED;
        $this->save();
        return $this;
    }

    /**
     * Marks the user as disabled
     *
     * @return User
     */
    public function disable(): User
    {
        $this->status = UserStatus::DISABLED;
        $this->save();
        return $this;
    }

    /**
     * Marks the user as active
     *
     * @return User
     */
    public function activate(): User
    {
        $this->status = UserStatus::ACTIVE;
        $this->save();
        return $this;
    }

    /**
     * Converts the model to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->accountStatus->status,
            'tokens' => $this->tokens,
        ];
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Dependency::class)->withTimestamps();
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $panel->getId() === 'admin';
    }

    public function getFilamentName(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->getFilamentName());
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    public function hasRole(RoleKeys $role): bool
    {
        return $this->roles()
            ->where('key', $role)
            ->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontLogIfAttributesChangedOnly(['last_login', 'last_login_ip', 'created_at', 'updated_at']);
    }
}
