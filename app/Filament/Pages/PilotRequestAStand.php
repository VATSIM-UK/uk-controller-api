<?php

namespace App\Filament\Pages;

use App\Models\Stand\StandRequest;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PilotRequestAStand extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationGroup = 'Preferences';
    protected static ?string $navigationLabel = 'Request a Stand';
    protected static ?string $title = 'Request a Stand';
    protected static ?string $slug = 'request-a-stand';
    protected static string $view = 'filament.pages.pilot-request-a-stand';

    protected $listeners = ['requestAStandFormSubmitted', 'currentStandRequestRelinquished'];

    public ?StandRequest $standRequest = null;

    public function mount(): void
    {
        $this->refreshStandRequest();
    }

    private function refreshStandRequest(): void
    {
        $this->standRequest = StandRequest::where('user_id', Auth::id())
            ->hasNotExpired()
            ->first();
    }

    public function currentStandRequestRelinquished()
    {
        $this->standRequest = null;
    }

    public function requestAStandFormSubmitted()
    {
        $this->refreshStandRequest();
    }
}
