<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WakeCategoryScheme extends Model
{
    private const RECAT_EU_KEY = 'RECAT_EU';
    private const UK_KEY = 'UK';

    protected $fillable = [
        'key',
    ];

    protected function scopeUk(Builder $query)
    {
        $query->where('key', self::UK_KEY);
    }

    protected function scopeRecat(Builder $query)
    {
        $query->where('key', self::RECAT_EU_KEY);
    }

    public function isRecat()
    {
        return $this->key === self::RECAT_EU_KEY;
    }

    public function isUk()
    {
        return $this->key === self::UK_KEY;
    }
}
