<?php
namespace App\Models\Squawks;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for a reserved squawk code.
 *
 * Class Reserved
 * @package App\Models\Squawks
 */
class Reserved extends Model
{
    protected $table = 'squawk_reserved';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'squawk',
    ];
}
