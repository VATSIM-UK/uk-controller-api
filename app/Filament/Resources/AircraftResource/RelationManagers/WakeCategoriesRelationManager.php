<?php

namespace App\Filament\Resources\AircraftResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

class WakeCategoriesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    protected static string $relationship = 'wakeCategories';
    protected static ?string $inverseRelationship = 'aircraft';
    protected static ?string $recordTitleAttribute = 'description';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scheme.name')
                    ->label(self::translateTablePath('columns.scheme_name')),
                Tables\Columns\TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code')),
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(
                        fn (RelationManager $livewire) => [
                            Forms\Components\Select::make('recordId')
                                ->label(self::translateFormPath('category.label'))
                                ->required()
                                ->options(
                                    function () use ($livewire) {
                                        $schemesInUse = WakeCategoryScheme::whereHas(
                                            'categories',
                                            function (Builder $category) use ($livewire) {
                                                $category->whereHas(
                                                    'aircraft',
                                                    function (Builder $aircraft) use ($livewire) {
                                                        $aircraft->where('aircraft.id', $livewire->ownerRecord->id);
                                                    }
                                                );
                                            }
                                        )->get()->pluck('id');

                                        return WakeCategory::with('scheme')
                                            ->whereNotIn('wake_category_scheme_id', $schemesInUse)
                                            ->orderBy('wake_category_scheme_id')
                                            ->orderBy('relative_weighting')
                                            ->get()
                                            ->mapWithKeys(function (WakeCategory $category) {
                                                return [
                                                    $category->id => sprintf(
                                                        '%s: %s',
                                                        $category->scheme->name,
                                                        $category->description
                                                    ),
                                                ];
                                            });
                                    }
                                ),
                        ]
                    ),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'aircraft.wake_categories';
    }
}
