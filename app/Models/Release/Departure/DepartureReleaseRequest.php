<?php

namespace App\Models\Release\Departure;

use App\Models\Controller\ControllerPosition;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartureReleaseRequest extends Model
{
    use SoftDeletes, HasFactory;

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
        'remarks',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
        'release_expires_at' => 'datetime',
        'released_at' => 'datetime',
        'rejected_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'release_valid_from' => 'datetime',
    ];

    /**
     * Approve the departure release for a given amount of time.
     */
    public function approve(
        int $userId,
        ?int $expiresInSeconds,
        CarbonImmutable $releaseValidFrom,
        string $remarks = ''
    ): void {
        $this->update(
            [
                'release_valid_from' => $releaseValidFrom,
                'released_by' => $userId,
                'released_at' => Carbon::now(),
                'release_expires_at' => $expiresInSeconds ? $releaseValidFrom->addSeconds($expiresInSeconds) : null,
                'remarks' => $remarks,
            ]
        );
    }

    /**
     * Reject the departure release
     */
    public function reject(int $userId, string $remarks = ''): void
    {
        $this->update(
            [
                'rejected_by' => $userId,
                'rejected_at' => Carbon::now(),
                'remarks' => $remarks,
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

    /*
     * A release request is determined to be "active" for a given callsign if:
     *
     *  1. It has not been rejected by the target controller AND
     *  2a. It has not been approved by the target controller and the release request hasn't expired OR
     *  2b. It has been approved by the controller AND
     *  2bi. It has no expiry time OR
     *  2bii. The expiry time has not yet been reached
     *
     * All of this, assuming that the model has not been deleted via SoftDeletes.
     */
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
                                ->where(
                                    function (Builder $builder) {
                                        $builder->whereNull('release_expires_at')
                                            ->orWhere('release_expires_at', '>', Carbon::now());
                                    }
                                );
                        }
                    );
                }
            );
    }

    public function scopeTarget(Builder $builder, int $targetController): Builder
    {
        return $builder->where('target_controller_position_id', $targetController);
    }
}
