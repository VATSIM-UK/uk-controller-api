<?php

namespace App\Filament\Resources\Pages;

trait LimitsTableRecordListingOptions
{
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 15, 25, 50];
    }
}
