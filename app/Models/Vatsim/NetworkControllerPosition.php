<?php

namespace App\Models\Vatsim;

use App\Helpers\Vatsim\ControllerPositionInterface;
use Illuminate\Database\Eloquent\Model;

class NetworkControllerPosition extends Model implements ControllerPositionInterface
{
    public $timestamps = true;

    protected $fillable = [
        'cid',
        'callsign',
        'frequency',
        'controller_position_id',
    ];

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
