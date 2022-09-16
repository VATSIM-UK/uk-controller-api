<?php

namespace App\Observers;

use App\Filament\Helpers\SelectOptions;
use Illuminate\Database\Eloquent\Model;

class SelectOptionsObserver
{
    public function created(Model $model): void
    {
        $this->clearCache($model);
    }

    public function updated(Model $model): void
    {
        $this->clearCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->clearCache($model);
    }

    private function clearCache(Model $model): void
    {
        SelectOptions::clearCache(get_class($model));
    }
}
