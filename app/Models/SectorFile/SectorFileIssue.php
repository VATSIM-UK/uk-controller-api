<?php

namespace App\Models\SectorFile;

use Illuminate\Database\Eloquent\Model;

class SectorFileIssue extends Model
{
    protected $fillable = [
        'issue_number',
        'issue_url'
    ];
}
