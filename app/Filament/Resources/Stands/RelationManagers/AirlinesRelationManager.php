<?php

namespace App\Filament\Resources\Stands\RelationManagers;

use App\Filament\Helpers\PairsAirlinesWithStands;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AirlinesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithStands;
    use TranslatesStrings;

    protected bool $allowsDuplicates = true;

    protected static string $relationship = 'airlines';

    protected static ?string $inverseRelationship = 'stands';

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
                ...self::airlineStandPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-airline')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->form(fn (AttachAction $action): array => [
                        $action
                            ->recordSelectSearchColumns(['icao_code', 'name'])
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineStandPairingFormFields(),
                    ]),
            ])
            ->recordActions([
                EditAction::make('edit-airline-pairing')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('update'))
                    ->schema(self::airlineStandPairingFormFields()),
                DetachAction::make('unpair-airline')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach'))
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure()),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'stands.airlines';
    }
}
