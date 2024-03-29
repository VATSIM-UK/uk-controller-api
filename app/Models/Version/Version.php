<?php

namespace App\Models\Version;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model for a plugin version.
 *
 * Class Version
 * @package App\Models
 */
class Version extends Model
{
    use SoftDeletes;

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
        'plugin_release_channel_id',
        'deleted_at',
    ];

    /**
     * Revoke a version and make it not allowed
     *
     * @return this
     */
    public function toggleAllowed(): Version
    {
        if ($this->trashed()) {
            $this->restore();
        } else {
            $this->delete();
        }

        return $this;
    }

    public function pluginReleaseChannel(): BelongsTo
    {
        return $this->belongsTo(PluginReleaseChannel::class);
    }

    public function scopeReleaseChannel(Builder $builder, PluginReleaseChannel $channel): Builder
    {
        return $builder->where('plugin_release_channel_id', $channel->id);
    }
}
