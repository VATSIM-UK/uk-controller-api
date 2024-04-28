<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\TerminalResource\Pages;
use App\Filament\Resources\TerminalResource\RelationManagers\AirlinesRelationManager;
use App\Models\Airfield\Terminal;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class TerminalResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Terminal::class;
    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static ?string $navigationGroup = 'Airfield';
    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('airfield_id')
                    ->required()
                    ->label(self::translateFormPath('airfield.label'))
                    ->options(SelectOptions::airfields())
                    ->searchable(!App::runningUnitTests())
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->required(),
                TextInput::make('description')
                    ->required()
                    ->label(self::translateFormPath('description.label'))
                    ->minLength(1)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
                Tables\Columns\TextColumn::make('airlines')
                    ->label(self::translateTablePath('columns.airlines'))
                    ->formatStateUsing(fn (Collection $state) => $state->count()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            AirlinesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTerminals::route('/'),
            'create' => Pages\CreateTerminal::route('/create'),
            'edit' => Pages\EditTerminal::route('/{record}/edit'),
            'view' => Pages\ViewTerminal::route('/{record}'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'terminals';
    }
}
