<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use App\Models\Stand\Stand;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PairedStandsRelationManager extends RelationManager
{
    protected static string $relationship = 'pairedStands';
    protected static ?string $inverseRelationship = 'pairedStands';

    protected static ?string $recordTitleAttribute = 'identifier';

    protected function getTableDescription(): ?string
    {
        return __('table.stands.paired.description');
    }

    public static function table(Table $table): Table
    {
        $attachAction = Tables\Actions\AttachAction::make('pair-stand');
        $detachAction = Tables\Actions\DetachAction::make('unpair-stand');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('table.stands.paired.columns.id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(__('table.stands.paired.columns.airfield'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('table.stands.paired.columns.identifier'))
                    ->sortable()
                    ->searchable(),
            ])
            ->headerActions([
                $attachAction->form(fn(): array => [
                    Select::make('recordId')
                        ->required()
                        ->options(
                            $attachAction->getRelationship()
                                ->getRelated()
                                ->newModelQuery()
                                ->where('airfield_id', $attachAction->getRelationship()->getParent()->airfield_id)
                                ->where('id', '<>', $attachAction->getRelationship()->getParent()->id)
                                ->whereDoesntHave('pairedStands', function (Builder $pairedStand) use ($attachAction) {
                                    $pairedStand->where(
                                        'stand_pairs.paired_stand_id',
                                        $attachAction->getRelationship()->getParent()->id
                                    );
                                })
                                ->get()
                                ->mapWithKeys(
                                    fn(Stand $stand) => [
                                        $stand->{$attachAction->getRelationship()->getRelatedKeyName(
                                        )} => $attachAction->getRecordTitle($stand),
                                    ]
                                )
                        )
                        ->searchable(!App::runningUnitTests())
                        ->label(__('form.stands.pairs.stand.label'))
                        ->helperText(__('form.stands.pairs.stand.helper'))
                        ->disableLabel(false),
                ])
                    ->using(function (array $data) use ($attachAction) {
                        DB::transaction(function () use ($attachAction, $data) {
                            $stand = $attachAction->getRelationship()->getParent();
                            $pairedStand = Stand::findOrFail($data['recordId']);
                            $stand->pairedStands()->attach($pairedStand, ['stand_id' => $stand->id]);
                            $pairedStand->pairedStands()->attach($stand, ['stand_id' => $pairedStand->id]);

                            return $data;
                        });
                    })
                    ->label(__('form.stands.pairs.add.label'))
            ])
            ->actions([
                $detachAction->using(
                    function (Stand $record) use ($detachAction): void {
                        DB::transaction(function () use ($record, $detachAction) {
                            $detachAction->getRelationship()->detach($record);
                            $record->pairedStands()->detach($detachAction->getRelationship()->getParent());
                        });
                    }
                )
                    ->label(__('form.stands.pairs.detach.label')),
            ]);
    }
}
