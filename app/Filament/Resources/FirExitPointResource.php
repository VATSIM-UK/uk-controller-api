<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FirExitPointResource\Pages;
use App\Models\IntentionCode\ConditionType;
use App\Models\IntentionCode\FirExitPoint;
use App\Models\IntentionCode\IntentionCode;
use App\Rules\Heading\ValidHeading;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class FirExitPointResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = FirExitPoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-x-mark';
    protected static ?string $navigationGroup = 'Intention Codes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('exit_point')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(5)
                    ->label(self::translateFormPath('exit_point.label'))
                    ->helperText(self::translateFormPath('exit_point.helper')),
                Toggle::make('internal')
                    ->label(self::translateFormPath('internal.label'))
                    ->helperText(self::translateFormPath('internal.helper'))
                    ->required(),
                Section::make('exit_cone')
                    ->heading(self::translateFormPath('exit_cone.heading'))
                    ->description(self::translateFormPath('exit_cone.description'))
                    ->schema([
                        TextInput::make('exit_direction_start')
                            ->required()
                            ->rule(new ValidHeading())
                            ->label(self::translateFormPath('exit_direction_start.label'))
                            ->helperText(self::translateFormPath('exit_direction_start.helper')),
                        TextInput::make('exit_direction_end')
                            ->required()
                            ->rule(new ValidHeading())
                            ->label(self::translateFormPath('exit_direction_end.label'))
                            ->helperText(self::translateFormPath('exit_direction_end.helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exit_point')
                    ->label(self::translateTablePath('columns.exit_point'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('internal')
                    ->boolean()
                    ->label(self::translateTablePath('columns.internal')),
                TextColumn::make('exit_direction_start')
                    ->label(self::translateTablePath('columns.exit_direction_start')),
                TextColumn::make('exit_direction_end')
                    ->label(self::translateTablePath('columns.exit_direction_end')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (DeleteAction $action) {
                        $hasIntentionCodes = IntentionCode::all()
                            ->filter(
                                fn (IntentionCode $intentionCode) => self::hasExitPointCondition(
                                    $intentionCode->conditions,
                                    $action->getRecord()
                                )
                            )
                            ->isNotEmpty();

                        if ($hasIntentionCodes) {
                            Notification::make('cannot-delete-exit-point')
                                ->warning()
                                ->title('Exit point cannot be deleted')
                                ->body('This exit point has intention codes associated with it.')
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ]);
    }

    private static function hasExitPointCondition(array $conditions, FirExitPoint $exitPoint): bool
    {
        foreach ($conditions as $condition) {
            if (
                ConditionType::from($condition['type']) === ConditionType::ExitPoint &&
                $condition['exit_point'] === $exitPoint->id
            ) {
                return true;
            }

            if (
                in_array(
                    ConditionType::from($condition['type']),
                    [ConditionType::AllOf, ConditionType::AnyOf, ConditionType::Not]
                ) &&
                self::hasExitPointCondition($condition['conditions'], $exitPoint)
            ) {
                return true;
            }
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFirExitPoints::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'fir_exit_points';
    }
}
