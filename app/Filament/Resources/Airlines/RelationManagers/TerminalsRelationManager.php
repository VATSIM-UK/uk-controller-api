<?php

namespace App\Filament\Resources\Airlines\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachAction;
use Filament\Actions\BulkAction;
use App\Filament\Helpers\PairsAirlinesWithTerminals;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TerminalsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use PairsAirlinesWithTerminals;
    use TranslatesStrings;

    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'terminals';
    protected static ?string $inverseRelationship = 'airlines';
    protected static ?string $recordTitleAttribute = 'description';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('terminal_id')
                    ->formatStateUsing(
                        fn (Terminal $record) => sprintf('%s / %s', $record->airfield->code, $record->description)
                    )
                    ->label(self::translateTablePath('columns.terminal'))
                    ->sortable()
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->where(
                            'description',
                            'like',
                            '%' . $search . '%'
                        )
                            ->orWhereIn(
                                'airfield_id',
                                Airfield::where('code', 'like', '%' . $search . '%')->pluck('id')
                            )
                    ),
                ...self::airlineTerminalPairingTableColumns(),
            ])
            ->headerActions([
                AttachAction::make('pair-terminal')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->form(fn (AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Terminal $record): string => $record->airfieldDescription)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        ...self::airlineTerminalPairingFormFields(),
                    ]),
            ])
            ->recordActions([
                EditAction::make('edit-terminal-pairing')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('edit'))
                    ->schema(self::airlineTerminalPairingFormFields()),
                DetachAction::make('unpair-terminal')
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach'))
                    ->label(self::translateFormPath('remove.label'))
                    ->using(self::unpairingClosure()),
                
            ])
            ->toolbarActions([
                BulkAction::make('bulk-unpair-terminal')
                    ->label(self::translateFormPath('remove.label'))
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $unpair = self::unpairingClosure();

                        $records->each(function (Terminal $record) use ($unpair) {
                            $action = DetachAction::make('bulk-unpair-terminal');
                            $action->record($record);

                            $unpair($action);
                        });
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines.terminals';
    }
}
