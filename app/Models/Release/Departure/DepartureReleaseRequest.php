<?php

namespace App\Models\Release\Departure;

use App\Models\Controller\ControllerPosition;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartureReleaseRequest extends Model
{
    use SoftDeletes;

    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'user_id',
        'controller_position_id',
        'target_controller_position_id',
        'expires_at',
        'release_expires_at',
        'release_valid_from',
        'released_at',
        'released_by',
        'rejected_at',
        'rejected_by',
        'acknowledged_at',
        'acknowledged_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
        'release_expires_at',
        'released_at',
        'rejected_at',
        'acknowledged_at',
        'release_valid_from',
    ];

    /**
     * Approve the departure release for a given amount of time.
     */
    public function approve(int $userId, int $expiresInSeconds, CarbonImmutable $releaseValidFrom): void
    {
        $this->update(
            [
                'release_valid_from' => $releaseValidFrom,
                'released_by' => $userId,
                'released_at' => Carbon::now(),
                'release_expires_at' => $releaseValidFrom->addSeconds($expiresInSeconds),
            ]
        );
    }

    /**
     * Reject the departure release
     */
    public function reject(int $userId): void
    {
        $this->update(
            [
                'rejected_by' => $userId,
                'rejected_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Acknowledge the departure release but take no further
     * action.
     */
    public function acknowledge(int $userId): void
    {
        $this->update(
            [
                'acknowledged_by' => $userId,
                'acknowledged_at' => Carbon::now(),
            ]
        );
    }

    public function cancel()
    {
        $this->delete();
    }

    public function decisionMade(): bool
    {
        return $this->released_by !== null || $this->rejected_by !== null;
    }

    public function scopeActiveFor(Builder $builder, string $callsign): Builder
    {
        return $builder->where('callsign', $callsign)
            ->whereNull('rejected_at')
            ->where(
                function (Builder $builder) {
                    return $builder->where(
                        function (Builder $builder) {
                            $builder->where('expires_at', '>', Carbon::now())
                                ->whereNull('released_at');
                        }
                    )->orWhere(
                        function (Builder $builder) {
                            $builder->whereNotNull('released_at')
                                ->where('release_expires_at', '>', Carbon::now());
                        }
                    );
                }
            );
    }
}
