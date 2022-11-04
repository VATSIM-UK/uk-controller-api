<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits;

use App\Filament\Resources\UnitDiscreteSquawkRangeResource;

trait MutatesRuleData
{
    private static function mutateFormData(): callable
    {
        return function (array $data): array {
            if ($data['service']) {
                $data['rules'][] = [
                    'type' => 'SERVICE',
                    'rule' => $data['service'],
                ];
            }

            if ($data['flight_rules']) {
                $data['rules'][] = [
                    'type' => 'FLIGHT_RULES',
                    'rule' => $data['flight_rules'],
                ];
            }

            if ($data['unit_type']) {
                $data['rules'][] = [
                    'type' => 'UNIT_TYPE',
                    'rule' => $data['unit_type'],
                ];
            }

            return $data;
        };
    }
}
