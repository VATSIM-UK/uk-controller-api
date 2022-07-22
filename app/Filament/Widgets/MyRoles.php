<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class MyRoles extends Widget
{
    protected static string $view = 'filament.widgets.my-roles';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
