<?php

namespace App\Models\Airfield;

use App\Models\Mapping\MappingElement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Location\Coordinate;

class VisualReferencePoint extends Model implements MappingElement
{
    protected $fillable = [
        'name',
        'short_name',
        'latitude',
        'longitude',
    ];

    public function airfields(): BelongsToMany
    {
        return $this->belongsToMany(Airfield::class);
    }

    public function elementType(): string
    {
        return 'visual_reference_point';
    }

    public function elementName(): string
    {
        return $this->name;
    }

    public function elementCoordinate(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }
}
