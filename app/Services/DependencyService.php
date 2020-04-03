<?php

namespace App\Services;

use App\Models\Dependency\Dependency;

class DependencyService
{
    public static function touchDependency(string $key)
    {
        $dependency = Dependency::where('key', $key)->first();
        $dependency && $dependency->touch();
    }
}
