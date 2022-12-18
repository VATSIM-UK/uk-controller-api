<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Models\IntentionCode\ConditionType;

trait FillsIntentionCodeForms
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            'id' => $data['id'],
            'description' => $data['description'],
            'code_type' => $data['code']['type'],
            'single_code' => $data['code']['type'] === 'single_code' ? $data['code']['code'] : null,
            'conditions' => $this->fillConditions($data['conditions']),
            'order_type' => 'at_position',
            'position' => $data['priority'],
        ];
    }

    private function fillConditions(array $conditions): array
    {
        $filledConditions = [];
        foreach ($conditions as $condition) {
            $filledConditions[] = [
                'type' => $condition['type'],
                'data' => [
                    ...match (ConditionType::from($condition['type'])) {
                        ConditionType::ArrivalAirfields => $this->fillArrivalAirfieldsCondition($condition),
                        ConditionType::ArrivalAirfieldPattern => $this->fillArrivalAirfieldPatternCondition($condition),
                        ConditionType::ExitPoint => $this->fillExitPointCondition($condition),
                        ConditionType::MaximumCruisingLevel => $this->fillMaximumCruisingLevelCondition($condition),
                        ConditionType::CruisingLevelAbove => $this->fillCruisingLevelAboveCondition($condition),
                        ConditionType::RoutingVia => $this->fillRoutingViaCondition($condition),
                        ConditionType::ControllerPositionStartsWith => $this->fillControllerPositionStartsWithCondition(
                            $condition
                        ),
                        ConditionType::Not, ConditionType::AnyOf, ConditionType::AllOf => $this->fillNestedCondition(
                            $condition
                        ),
                    }
                ],
            ];
        }

        return $filledConditions;
    }

    private function fillArrivalAirfieldsCondition(array $condition): array
    {
        return [
            'airfields' => array_map(
                fn(string $airfield) => ['airfield' => $airfield],
                $condition['airfields']
            ),
        ];
    }

    private function fillArrivalAirfieldPatternCondition(array $condition): array
    {
        return [
            'pattern' => $condition['pattern'],
        ];
    }

    private function fillExitPointCondition(array $condition): array
    {
        return [
            'exit_point' => $condition['exit_point'],
        ];
    }

    private function fillMaximumCruisingLevelCondition(array $condition): array
    {
        return [
            'maximum_cruising_level' => $condition['level'],
        ];
    }

    private function fillCruisingLevelAboveCondition(array $condition): array
    {
        return [
            'cruising_level_above' => $condition['level'],
        ];
    }

    private function fillRoutingViaCondition(array $condition): array
    {
        return [
            'routing_via' => $condition['point'],
        ];
    }

    private function fillControllerPositionStartsWithCondition(array $condition): array
    {
        return [
            'controller_position_starts_with' => $condition['starts_with'],
        ];
    }

    private function fillNestedCondition(array $condition): array
    {
        return [
            'conditions' => $this->fillConditions($condition['conditions']),
        ];
    }
}
