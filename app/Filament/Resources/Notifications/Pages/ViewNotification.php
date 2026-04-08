<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    public function beforeFill(): void
    {
        $this->getRecord()->readBy()->attach(Auth::user());
    }
}
