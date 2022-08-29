<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
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
