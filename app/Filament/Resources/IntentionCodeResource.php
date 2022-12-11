<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntentionCodeResource\Pages;
use App\Models\IntentionCode\FirExitPoint;
use App\Models\IntentionCode\IntentionCode;
use App\Rules\Airfield\AirfieldIcao;
use App\Rules\Airfield\PartialAirfieldIcao;
use App\Rules\Controller\ControllerPositionPartialCallsign;
use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class IntentionCodeResource extends Resource
{
    protected static ?string $model = IntentionCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-code';
    protected static ?string $navigationGroup = 'Intention Codes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('code_spec')
                    ->schema([
                        Select::make('code_type')
                            ->required()
                            ->reactive()
                            ->options([
                                'airfield_identifier' => 'Airfield Identifier',
                                'single_code' => 'Single Code',
                            ]),
                        TextInput::make('single_code')
                            ->required(fn(Closure $get) => $get('code_type') === 'single_code')
                            ->maxLength(2)
                            ->hidden(fn(Closure $get) => $get('code_type') !== 'single_code')
                    ]),
                self::conditions(),
                Select::make('insert_before')
                    ->options(fn() => IntentionCode::all()->mapWithKeys(fn(IntentionCode $code) => [$code->id => self::formatCodeColumn($code)])
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priority'),
                TextColumn::make('code')
                    ->formatStateUsing(fn(IntentionCode $record) => self::formatCodeColumn($record)),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIntentionCodes::route('/'),
            'create' => Pages\CreateIntentionCode::route('/create'),
            'edit' => Pages\EditIntentionCode::route('/{record}/edit'),
        ];
    }

    private static function formatCodeColumn(IntentionCode $record): string
    {
        return match ($record->code['type']) {
            'airfield_identifier' => 'Airfield Identifier',
            'single_code' => $record->code['code'],
        };
    }

    private static function conditions(): Builder
    {
        return Builder::make('conditions')
            ->required()
            ->blocks([
                Block::make('arrival_airfields')
                    ->schema([
                        Repeater::make('airfields')
                            ->schema([
                                TextInput::make('airfield')
                                    ->required()
                                    ->rule(new AirfieldIcao())
                            ]),
                    ]),
                Block::make('arrival_airfield_pattern')
                    ->schema([
                        TextInput::make('pattern')
                            ->required()
                            ->rule(new PartialAirfieldIcao()),
                    ]),
                Block::make('exit_point')
                    ->schema([
                        Select::make('exit_point')
                            ->required()
                            ->searchable()
                            ->options(
                                FirExitPoint::all()
                                    ->mapWithKeys(fn(FirExitPoint $firExitPoint) => [$firExitPoint->id => $firExitPoint->exit_point])
                            )

                    ]),
                Block::make('maximum_cruising_level')
                    ->schema([
                        TextInput::make('maximum_cruising_level')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(60000)
                    ]),
                Block::make('cruising_level_above')
                    ->schema([
                        TextInput::make('cruising_level_above')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(60000),
                    ]),
                Block::make('routing_via')
                    ->schema([
                        TextInput::make('routing_via')
                            ->required()
                            ->maxLength(5)
                    ]),
                Block::make('controller_position_starts_with')
                    ->schema([
                        TextInput::make('controller_position_starts_with')
                            ->required()
                            ->rule(new ControllerPositionPartialCallsign()),
                    ]),
                Block::make('not')
                    ->schema(fn() => [self::conditions()]),
                Block::make('any_of')
                    ->schema(fn() => [self::conditions()])
            ]);
    }
}
