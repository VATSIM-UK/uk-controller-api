<?php
namespace App\Models\User;

use App\Models\Dependency\Dependency;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\HasApiTokens;

/**
 * Model for a user of the plugin.
 *
 * Class User
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

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
        'last_login',
        'last_version',
    ];

    /**
     * Returns the relation to the users status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function accountStatus() : HasOne
    {
        return $this->hasOne(UserStatus::class, 'id', 'status');
    }

    /**
     * Sets the last login time of the user
     *
     * @return User
     */
    public function touchLastLogin() : User
    {
        $this->last_login = Carbon::now();
        $this->save();
        return $this;
    }

    /**
     * Sets the users last logged in version
     *
     * @param integer $versionId Id of the version
     * @return this
     */
    public function setLastVersion(int $versionId) : User
    {
        $this->last_version = $versionId;
        $this->save();
        return $this;
    }

    /**
     * Marks the user as banned
     *
     * @return User
     */
    public function ban() : User
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
    public function disable() : User
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
    public function activate() : User
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
    public function jsonSerialize() : array
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
}
