<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Models\IntentionCode\ConditionType;
use App\Models\IntentionCode\IntentionCode;

trait MutatesIntentionCodes
{
    private function mutateIntentionCode(array $data): array
    {
        return [
            ...$this->mutateId($data),
            'code' => $this->mutateCode($data),
            'conditions' => $this->mutateConditions($data),
            'priority' => $this->mutatePriority($data),
        ];
    }

    private function mutateId(array $data): array
    {
        return isset($data['id']) ? ['id' => $data['id']] : [];
    }

    private function mutateCode(array $data): array
    {
        return match ($data['code_type']) {
            'airfield_identifier' => ['type' => 'airfield_identifier'],
            'single_code' => ['type' => 'single_code', 'code' => $data['single_code']],
        };
    }

    private function mutateConditions(array $data): array
    {
        $mutatedConditions = [];
        foreach ($data['conditions'] as $condition) {
            $mutatedConditions[] = [
                'type' => $condition['type'],
                ...match (ConditionType::from($condition['type'])) {
                    ConditionType::ArrivalAirfields => $this->mutateArrivalAirfields($condition),
                    ConditionType::ArrivalAirfieldPattern => $this->mutateArrivalAirfieldPattern($condition),
                    ConditionType::ExitPoint => $this->mutateExitPoint($condition),
                    ConditionType::MaximumCruisingLevel => $this->mutateMaxLevel($condition),
                    ConditionType::CruisingLevelAbove => $this->mutateCruisingLevelAbove($condition),
                    ConditionType::RoutingVia => $this->mutateRoutingVia($condition),
                    ConditionType::ControllerPositionStartsWith => $this->mutateControllerPositionStartsWith($condition),
                    ConditionType::Not, ConditionType::AnyOf, ConditionType::AllOf => $this->mutateNestedConditions($condition),
                }
            ];
        }

        return $mutatedConditions;
    }

    private function mutateArrivalAirfields(array $condition): array
    {
        return [
            'airfields' => array_map(fn(array $airfield) => $airfield['airfield'], $condition['data']['airfields']),
        ];
    }

    private function mutateArrivalAirfieldPattern(array $condition): array
    {
        return [
            'pattern' => $condition['data']['pattern'],
        ];
    }

    private function mutateExitPoint(array $condition): array
    {
        return [
            'exit_point' => (int) $condition['data']['exit_point'],
        ];
    }

    private function mutateMaxLevel(array $condition): array
    {
        return [
            'level' => (int) $condition['data']['maximum_cruising_level'],
        ];
    }

    private function mutateCruisingLevelAbove(array $condition): array
    {
        return [
            'level' => (int) $condition['data']['cruising_level_above'],
        ];
    }

    private function mutateRoutingVia(array $condition): array
    {
        return [
            'point' => $condition['data']['routing_via'],
        ];
    }

    private function mutateControllerPositionStartsWith(array $condition): array
    {
        return [
            'starts_with' => $condition['data']['controller_position_starts_with'],
        ];
    }

    private function mutateNestedConditions(array $condition): array
    {
        return [
            'conditions' => $this->mutateConditions($condition['data']),
        ];
    }

    private function mutatePriority(array $data): int
    {
        return match ($data['order_type']) {
            'at_position' => $this->getFixedInsertPosition((int) $data['position']),
            'before' => IntentionCode::findOrFail($data['insert_position'])->priority,
            'after' => IntentionCode::findOrFail($data['insert_position'])->priority + 1,
        };
    }

    private function getFixedInsertPosition(int $position): int
    {
        $maxInsertPosition = IntentionCode::max('priority') + 1;
        return $position > $maxInsertPosition ? $maxInsertPosition : $position;
    }
}
