<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DepartureStandFinder extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';
    protected static string | \UnitEnum | null $navigationGroup = 'Preferences';
    protected static ?string $navigationLabel = 'Find a Departure Stand';
    protected static ?string $title = 'Find a Departure Stand';
    protected static ?string $slug = 'find-departure-stand';
    protected string $view = 'filament.pages.departure-stand-finder';

    protected $listeners = ['departureStandFinderFormSubmitted'];

    public ?array $result = null;

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
    }

    public function departureStandFinderFormSubmitted(array $data): void
    {
        $this->result = $data;
    }
}
