<?php

namespace App\Models\Acars;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class AcarsMessage extends Model
{
    use MassPrunable;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'message',
        'successful',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    public function prunable(): Builder
    {
        return static::where('created_at', '<', Carbon::now()->subMonth());
    }
}
