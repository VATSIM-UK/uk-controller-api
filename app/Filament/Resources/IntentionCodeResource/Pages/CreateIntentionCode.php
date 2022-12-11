<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIntentionCode extends CreateRecord
{
    protected static string $resource = IntentionCodeResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'code' => $this->mutateCode($data),
            'conditions' => $this->formatConditions($data),
            'priority' => rand(0, 9999999),
        ];
    }

    private function mutateCode(array $data): array
    {
        return match ($data['code_type']) {
            'airfield_identifier' => ['type' => 'airfield_identifier'],
            'single_code' => ['type' => 'single_code', 'code' => $data['single_code']],
        };
    }

    private function formatConditions(array $data): array
    {
        $mutatedConditions = [];
        foreach ($data['conditions'] as $condition) {
            $mutatedConditions[] = match ($condition['type']) {
                'arrival_airfields' => $this->formatArrivalAirfields($condition),
                'arrival_airfield_pattern' => $this->formatArrivalAirfieldPattern($condition),
                'exit_point' => $this->formatExitPoint($condition),
                'maximum_cruising_level' => $this->formatMaxLevel($condition),
                'cruising_level_above' => $this->formatCruisingLevelAbove($condition),
                'routing_via' => $this->formatRoutingVia($condition),
                'controller_position_starts_with' => $this->formatControllerPositionStartsWith($condition),
                'not', 'any_of' => $this->formatNestedConditions($condition),
            };
        }

        return $mutatedConditions;
    }

    private function formatArrivalAirfields(array $condition): array
    {
        return [
            'type' => 'arrival_airfields',
            'airfields' => array_map(fn(array $airfield) => $airfield['airfield'], $condition['data']['airfields']),
        ];
    }

    private function formatArrivalAirfieldPattern(array $condition): array
    {
        return [
            'type' => 'arrival_airfield_pattern',
            'pattern' => $condition['data']['pattern'],
        ];
    }

    private function formatExitPoint(array $condition): array
    {
        return [
            'type' => 'exit_point',
            'exit_point' => (int) $condition['data']['exit_point'],
        ];
    }

    private function formatMaxLevel(array $condition): array
    {
        return [
            'type' => 'maximum_cruising_level',
            'level' => (int) $condition['data']['maximum_cruising_level'],
        ];
    }

    private function formatCruisingLevelAbove(array $condition): array
    {
        return [
            'type' => 'cruising_level_above',
            'level' => (int) $condition['data']['cruising_level_above'],
        ];
    }

    private function formatRoutingVia(array $condition): array
    {
        return [
            'type' => 'routing_via',
            'point' => $condition['data']['routing_via'],
        ];
    }

    private function formatControllerPositionStartsWith(array $condition): array
    {
        return [
            'type' => 'controller_position_starts_with',
            'starts_with' => $condition['data']['controller_position_starts_with'],
        ];
    }

    private function formatNestedConditions(array $condition): array
    {
        return [
            'type' => $condition['type'],
            'conditions' => $this->formatConditions($condition['data']),
        ];
    }
}
