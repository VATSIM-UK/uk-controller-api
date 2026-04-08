<?php

namespace App\Filament\Resources\Terminals\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachAction;
use App\Filament\Helpers\PairsAirlinesWithTerminals;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AirlinesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithTerminals;
    use TranslatesStrings;

    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'terminals';
    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->columns([
                TextColumn::make('icao_code')
                    ->label(self::translateTablePath('columns.icao'))
                    ->sortable()
                    ->searchable(),
                ...self::airlineTerminalPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-airline')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineTerminalPairingFormFields(),
                    ])
            ])
            ->recordActions([
                EditAction::make('edit-airline-pairing')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('edit'))
                    ->schema(self::airlineTerminalPairingFormFields()),
                DetachAction::make('unpair-airline')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach'))
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure())
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'terminals.airlines';
    }
}
