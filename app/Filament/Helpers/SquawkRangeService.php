<?php

namespace App\Filament\Helpers;

enum SquawkRangeService: string
{
    case Basic = 'BASIC';
    case Traffic = 'TRAFFIC';
    case Deconfliction = 'DECON';
    case Procedural = 'PROCEDURAL';
}
