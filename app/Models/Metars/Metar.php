<?php

namespace App\Models\Metars;

use App\Exceptions\MetarException;
use Illuminate\Database\Eloquent\Model;

class Metar extends Model
{
    protected $fillable = [
        'airfield_id',
        'metar_string',
    ];

    protected $casts = [
        'airfield_id' => 'integer',
    ];

    public function getQnh(): ?int
    {
        $matches = [];
        preg_match('/Q\d{4}/', $this->metar_string, $matches);

        if (empty($matches)) {
            return null;
        }

        $value = substr($matches[0], 1);
        return (int) ($value[0] === '0') ? substr($value, 1) : $value;
    }
}
