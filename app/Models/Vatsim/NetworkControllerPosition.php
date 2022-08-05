<?php

namespace App\Models\Vatsim;

use App\Helpers\Vatsim\ControllerPositionInterface;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkControllerPosition extends Model implements ControllerPositionInterface
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'cid',
        'callsign',
        'frequency',
        'controller_position_id',
    ];

    public function controllerPosition(): BelongsTo
    {
        return $this->belongsTo(ControllerPosition::class);
    }

    public function getCallsign(): string
    {
        return $this->callsign;
    }

    public function getFrequency(): float
    {
        return $this->frequency;
    }

    public function clearActiveControllerPosition(): void
    {
        $this->controller_position_id = null;
        $this->save();
    }

    public function setActiveControllerPosition(int $positionId): void
    {
        $this->controller_position_id = $positionId;
        $this->save();
    }
}
