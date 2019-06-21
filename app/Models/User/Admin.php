<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    public $timestamps = true;

    public $incrementing = false;

    public $table = 'admin';

    public $primaryKey ='user_id';

    protected $fillable = [
        'user_id',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
