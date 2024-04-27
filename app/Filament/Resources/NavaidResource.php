<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasCoordinates;
use App\Filament\Resources\NavaidResource\Pages;
use App\Filament\Resources\NavaidResource\RelationManagers;
use App\Models\Navigation\Navaid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class NavaidResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = Navaid::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationLabel = 'Navaids and Holds';
    protected static ?string $label = 'Navaids and Holds';
    protected static ?string $navigationGroup = 'Enroute';

    public static function getEloquentQuery(): Builder
    {
        return Navaid::with('holds');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('identifier')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(5)
                    ->label(self::translateFormPath('identifier.label'))
                    ->helperText(self::translateFormPath('identifier.helper'))
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord),
                self::latitudeInput(),
                self::longitudeInput()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')
                    ->sortable()
                    ->searchable()
                    ->label(self::translateTablePath('columns.identifier')),
                Tables\Columns\BooleanColumn::make('has_published_holds')
                    ->getStateUsing(function (Navaid $record) {
                        return $record->holds->isNotEmpty();
                    })
                    ->label(self::translateTablePath('columns.published_holds')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])->defaultSort('identifier');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HoldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavaids::route('/'),
            'create' => Pages\CreateNavaid::route('/create'),
            'view' => Pages\ViewNavaid::route('/{record}'),
            'edit' => Pages\EditNavaid::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'navaids';
    }
}
