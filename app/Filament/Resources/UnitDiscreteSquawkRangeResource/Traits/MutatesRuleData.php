<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits;

use App\Filament\Resources\UnitDiscreteSquawkRangeResource;

trait MutatesRuleData
{
    private static function mutateFormData(): callable
    {
        return function (array $data): array {
            if (!$data['rule_type']) {
                $data['rule'] = null;
                return $data;
            } elseif ($data['rule_type'] === UnitDiscreteSquawkRangeResource::RULE_TYPE_UNIT_TYPE) {
                $data['rule'] = [
                    'type' => $data['rule_type'],
                    'rule' => $data['unit_type'],
                ];
            } elseif ($data['rule_type'] === UnitDiscreteSquawkRangeResource::RULE_TYPE_FLIGHT_RULES) {
                $data['rule'] = [
                    'type' => $data['rule_type'],
                    'rule' => $data['flight_rules'],
                ];
            } else {
                $data['rule'] = null;
            }

            return $data;
        };
    }
}