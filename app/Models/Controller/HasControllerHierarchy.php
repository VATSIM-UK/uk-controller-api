<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasControllerHierarchy
{
    public function controllers(): BelongsToMany;
}
