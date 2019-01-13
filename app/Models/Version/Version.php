<?php
namespace App\Models\Version;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a plugin version.
 *
 * Class Version
 * @package App\Models
 */
class Version extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'version';

    /**
     * We should timestamp this table.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'version',
        'allowed',
    ];

    /**
     * Get the allowed attribute
     *
     * @param int $allowed
     * @return bool
     */
    public function getAllowedAttribute(int $allowed) : bool
    {
        return (bool) $allowed;
    }

    /**
     * Revoke a version and make it not allowed
     *
     * @return this
     */
    public function toggleAllowed() : Version
    {
        $this->allowed = !$this->allowed;
        $this->save();
        return $this;
    }
}
