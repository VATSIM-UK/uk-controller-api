<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WakeCategory extends Model
{
    protected $fillable = [
        'code',
        'description',
        'created_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function aircraft() : HasMany
    {
        return $this->hasMany(Aircraft::class);
    }
}
