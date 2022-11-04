<?php

namespace App\Filament\Resources\UnitDiscreteSquawkRangeResource\Traits;

use App\Models\Squawk\UnitSquawkRangeRuleType;

trait MutatesRuleData
{
    private static function mutateFormData(): callable
    {
        return function (array $data): array {
            if ($data['service']) {
                $data['rules'][] = [
                    'type' => UnitSquawkRangeRuleType::Service,
                    'rule' => $data['service'],
                ];
            }

            if ($data['flight_rules']) {
                $data['rules'][] = [
                    'type' => UnitSquawkRangeRuleType::FlightRules,
                    'rule' => $data['flight_rules'],
                ];
            }

            if ($data['unit_type']) {
                $data['rules'][] = [
                    'type' => UnitSquawkRangeRuleType::UnitType,
                    'rule' => $data['unit_type'],
                ];
            }

            return $data;
        };
    }

    private static function mutateRecordData(): callable
    {
        return function (array $data): array {
            if (!$data['rules']) {
                return $data;
            }

            foreach ($data['rules'] as $rule) {
                $dataKey = match ($rule['type']) {
                    UnitSquawkRangeRuleType::UnitType->value => 'unit_type',
                    UnitSquawkRangeRuleType::FlightRules->value => 'flight_rules',
                    UnitSquawkRangeRuleType::Service->value => 'service',
                };
                $data[$dataKey] = $rule['rule'];
            }

            return $data;
        };
    }
}
