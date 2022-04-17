<?php

use App\Models\IntentionCode\IntentionCode;
use App\Services\IntentionCode\Builder\IntentionCodeBuilder;
use App\Services\IntentionCode\Builder\CodeBuilder;
use App\Services\IntentionCode\Builder\ConditionBuilder;
use Illuminate\Database\Migrations\Migration;

class AddIntentionCodeData extends Migration
{
    private const NORTH = [305, 55];
    private const NORTH_EAST = [0, 125];
    private const EAST = [35, 145];
    private const SOUTH_EAST = [55, 180];
    private const SOUTH = [125, 235];
    private const SOUTH_WEST = [180, 305];
    private const WEST = [215, 325];
    private const NORTH_WEST = [260, 360];

    private const AMSTERDAM_AIRFIELDS = [
        'EHAM',
        'EHEH',
        'EHLE',
        'EHSB',
        'EHYB',
        'EHVK',
        'EHMZ',
        'EHBD',
        'EHBK',
        'EHKD',
        'EHRD',
        'EHVB',
        'EHWO',
        'EHGR',
        'EHSE',
        'EHDP',
    ];

    private const BRUSSELS_PRIMARY_AIRFIELDS = [
        'EBBR',
        'EBCI',
        'EBAW',
        'EBMB',
        'EBCV',
        'EBLG',
    ];

    private const BRUSSELS_SECONDARY_AIRFIELDS = [
        'EBKT',
        'EBOS',
    ];

    private const DUBLIN_AIRFIELDS = [
        'EIDW',
        'EIME',
        'EIWT',
        'EINC',
        'EITM',
        'EITT'
    ];

    private const SCOTTISH_ONLY_EXIT_POINTS = [
        [
            'fix' => 'INKOB',
            'code' => 'E4',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'SOSIM',
            'code' => 'E5',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'KELLY',
            'code' => 'E7',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'TUPEM',
            'code' => 'E8',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'BELOX',
            'code' => 'E9',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'SUBUK',
            'code' => 'H6',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH,
        ],
        [
            'fix' => 'LAKEY',
            'code' => 'H7',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH,
        ],
        [
            'fix' => 'BINTI',
            'code' => 'U1',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH_WEST,
        ],
        [
            'fix' => 'TILNI',
            'code' => 'U2',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH_WEST,
        ],
        [
            'fix' => 'ERKIT',
            'code' => 'U3',
            'heading_start' => self::SOUTH_EAST,
            'heading_end' => self::SOUTH,
        ],
        [
            'fix' => 'ROVNI',
            'code' => 'U4',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH_WEST,
        ],
        [
            'fix' => 'ELNAB',
            'code' => 'U5',
            'heading_start' => self::SOUTH,
            'heading_end' => self::SOUTH,
        ],
        [
            'fix' => 'ADGEG',
            'code' => 'U6',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
        [
            'fix' => 'AKOKO',
            'code' => 'U7',
            'heading_start' => self::EAST,
            'heading_end' => self::SOUTH_EAST,
        ],
    ];

    const EAST_SOUTH_EAST_EXIT_POINTS = [
        [
            'fix' => 'TRACA',
            'code' => 'D2',
        ],
        [
            'fix' => 'MOTOX',
            'code' => 'D3',
        ],
        [
            'fix' => 'LONAM',
            'code' => 'F'
        ],
        [
            'fix' => 'INKOB',
            'code' => 'E4'
        ],
        [
            'fix' => 'SOSIM',
            'code' => 'E5'
        ],
        [
            'fix' => 'KELLY',
            'code' => 'E7'
        ],
        [
            'fix' => 'TUPEM',
            'code' => 'E8'
        ],
        [
            'fix' => 'BELOX',
            'code' => 'E9'
        ],
    ];

    const SOUTH_EAST_EXIT_POINTS = [
        [
            'fix' => 'RINTI',
            'code' => 'D4'
        ]
    ];

    const EAST_EXIT_POINTS = [
        [
            'fix' => 'SOMVA',
            'code' => 'C1'
        ],
        [
            'fix' => 'REDFA',
            'code' => 'C2'
        ],
        [
            'fix' => 'SASKI',
            'code' => 'C3'
        ],
        [
            'fix' => 'TOPPA',
            'code' => 'F'
        ],
        [
            'fix' => 'ROKAN',
            'code' => 'F'
        ],
        [
            'fix' => 'LAMSO',
            'code' => 'F'
        ],
        [
            'fix' => 'GODOS',
            'code' => 'F'
        ],
        [
            'fix' => 'MOLIX',
            'code' => 'F'
        ],
    ];

    const EAST_NORTH_EAST_EXIT_POINTS = [
        [
            'fix' => 'VAXIT',
            'code' => 'K1'
        ],
        [
            'fix' => 'TINAC',
            'code' => 'K2'
        ],
        [
            'fix' => 'GOREV',
            'code' => 'K2'
        ],
        [
            'fix' => 'PETIL',
            'code' => 'K2'
        ],
        [
            'fix' => 'INBOB',
            'code' => 'K3'
        ],
        [
            'fix' => 'LESRA',
            'code' => 'K3'
        ],
        [
            'fix' => 'SOPTO',
            'code' => 'K3'
        ],
        [
            'fix' => 'GOLUM',
            'code' => 'K3'
        ],
        [
            'fix' => 'PEPIN',
            'code' => 'Z1'
        ],
        [
            'fix' => 'ORVIK',
            'code' => 'Z2'
        ],
        [
            'fix' => 'KLONN',
            'code' => 'Z3'
        ],
        [
            'fix' => 'ALOTI',
            'code' => 'Z4'
        ],
        [
            'fix' => 'NIVUN',
            'code' => 'Z5'
        ],
        [
            'fix' => 'BEREP',
            'code' => 'Z6'
        ],
        [
            'fix' => 'RIGVU',
            'code' => 'Z7'
        ],
    ];

    const NORTH_EXIT_POINTS = [
        [
            'fix' => 'MATIK',
            'code' => 'R1',
        ],
        [
            'fix' => 'NALAN',
            'code' => 'R2',
        ],
        [
            'fix' => 'OSBON',
            'code' => 'R3',
        ],
        [
            'fix' => 'PEMOS',
            'code' => 'R5',
        ],
        [
            'fix' => 'RIXUN',
            'code' => 'R6',
        ],
        [
            'fix' => 'SOSAR',
            'code' => 'R7',
        ],
        [
            'fix' => 'GUNPA',
            'code' => 'R8',
        ],
    ];

    const NORTH_NORTH_WEST_EXIT_POINTS = [
        [
            'fix' => 'RATSU',
            'code' => 'R',
        ],
        [
            'fix' => 'LUSEN',
            'code' => 'Y8'
        ]
    ];

    const WEST_NORTH_WEST_EXIT_POINTS = [
        [
            'fix' => 'ATSIX',
            'code' => 'Y7',
        ],
        [
            'fix' => 'ORTAV',
            'code' => 'Y6',
        ],
        [
            'fix' => 'BALIX',
            'code' => 'Y5',
        ],
        [
            'fix' => 'ADODO',
            'code' => 'Y4',
        ],
        [
            'fix' => 'ERAKA',
            'code' => 'Y3',
        ],
        [
            'fix' => 'ETILO',
            'code' => 'Y2',
        ],
        [
            'fix' => 'GOMUP',
            'code' => 'Y1',
        ],
    ];

    const WEST_EXIT_POINTS = [
        [
            'fix' => 'IBROD',
            'code' => 'N7',
        ],
        [
            'fix' => 'AMLAD',
            'code' => 'N6',
        ],
        [
            'fix' => 'MIMKU',
            'code' => 'N5',
        ],
        [
            'fix' => 'APSOV',
            'code' => 'N4',
        ],
        [
            'fix' => 'KUGUR',
            'code' => 'N3',
        ],
        [
            'fix' => 'LUTOV',
            'code' => 'N2',
        ],
        [
            'fix' => 'NIBOG',
            'code' => 'N1',
        ],
        [
            'fix' => 'VATRY',
            'code' => 'S4',
        ],
        [
            'fix' => 'BAKUR',
            'code' => 'S3',
        ],
        [
            'fix' => 'SLANY',
            'code' => 'S2',
        ],
        [
            'fix' => 'BANBA',
            'code' => 'S',
        ],
        [
            'fix' => 'EVRIN',
            'code' => 'S',
        ],
        [
            'fix' => 'MOLAK',
            'code' => 'G1',
        ],
        [
            'fix' => 'NIPIT',
            'code' => 'G2',
        ],
        [
            'fix' => 'ERNAN',
            'code' => 'G3',
        ],
        [
            'fix' => 'DEGOS',
            'code' => 'G4',
        ],
        [
            'fix' => 'NIMAT',
            'code' => 'G5',
        ],
        [
            'fix' => 'NEVRI',
            'code' => 'G7',
        ],
        [
            'fix' => 'RUBEX',
            'code' => 'G9',
        ],
        [
            'fix' => 'BAGSO',
            'code' => 'L',
        ],
        [
            'fix' => 'BOYNE',
            'code' => 'L',
        ],
        [
            'fix' => 'LIPGO',
            'code' => 'T',
        ],
        [
            'fix' => 'MORAG',
            'code' => 'T',
        ],
        [
            'fix' => 'LEDGO',
            'code' => 'M1',
        ],
        [
            'fix' => 'MOPAT',
            'code' => 'M1',
        ],
        [
            'fix' => 'LESLU',
            'code' => 'M',
        ],
        [
            'fix' => 'NORLA',
            'code' => 'M',
        ],
        [
            'fix' => 'SAMON',
            'code' => 'M',
        ],
        [
            'fix' => 'BAGSO',
            'code' => 'L',
        ],
    ];

    const WEST_SOUTH_WEST_EXIT_POINTS = [
        [
            'fix' => 'ARKIL',
            'code' => 'A1',
        ],
        [
            'fix' => 'LULOX',
            'code' => 'A1',
        ],
        [
            'fix' => 'TURLU',
            'code' => 'A',
        ],
        [
            'fix' => 'GAPLI',
            'code' => 'A',
        ],
        [
            'fix' => 'BISKI',
            'code' => 'A',
        ],
        [
            'fix' => 'TAKAS',
            'code' => 'A',
        ],
    ];

    const SOUTH_EXIT_POINTS = [
        [
            'fix' => 'ANNET',
            'code' => 'B4',
        ],
        [
            'fix' => 'LIZAD',
            'code' => 'B4',
        ],
        [
            'fix' => 'GANTO',
            'code' => 'B5',
        ],
        [
            'fix' => 'SUPAP',
            'code' => 'B6',
        ],
        [
            'fix' => 'PEMAK',
            'code' => 'B7',
        ],
        [
            'fix' => 'SALCO',
            'code' => 'B3',
        ],
        [
            'fix' => 'MANIG',
            'code' => 'B2',
        ],
        [
            'fix' => 'SKESO',
            'code' => 'B',
        ],
        [
            'fix' => 'SKERY',
            'code' => 'B',
        ],
        //TODO: Add HERN

        [
            'fix' => 'NEVIL',
            'code' => 'W',
        ],
        [
            'fix' => 'ANGLO',
            'code' => 'W',
        ],
        [
            'fix' => 'PETAX',
            'code' => 'P',
        ],
    ];

    private int $priority = 1;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addHomeArrivals();
        $this->addIrishArrivals();
        $this->addBrusselsArrivals();
        $this->addAmsterdamArrivals();
        $this->addScottishOnlyExits();
        $this->addKonanExits();
        $this->addEtratExits();
        $this->addVeuleExits();
        $this->processDirectionalExits();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        IntentionCode::query()->delete();
    }

    private function addHomeArrivals(): void
    {
        IntentionCodeBuilder::begin()
            ->withPriority($this->getPriority())
            ->withCode(function (CodeBuilder $codeBuilder) {
                $codeBuilder->airfieldIdentifier();
            })
            ->withCondition(function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->arrivalAirfieldPattern('EG');
            })
            ->create();
    }

    private function addIrishArrivals(): void
    {
        $this->addAirfieldArrivals(
            'DW',
            self::DUBLIN_AIRFIELDS,
        );

        $this->addAirfieldArrivals(
            'NN',
            ['EINN'],
        );
    }

    private function addBrusselsArrivals(): void
    {
        $this->addAirfieldArrivals(
            'EB',
            self::BRUSSELS_PRIMARY_AIRFIELDS,
            function (ConditionBuilder $conditionBuilder) {
                $this->applyNotScottishControllers($conditionBuilder->routingVia('KOK'));
            }
        );

        $this->addAirfieldArrivals(
            'ES',
            self::BRUSSELS_SECONDARY_AIRFIELDS,
            function (ConditionBuilder $conditionBuilder) {
                $this->applyNotScottishControllers($conditionBuilder->routingVia('KOK'));
            }
        );
    }

    private function addAmsterdamArrivals(): void
    {
        $this->addAirfieldArrivals(
            'AS',
            self::AMSTERDAM_AIRFIELDS,
            function (ConditionBuilder $conditionBuilder) {
                $this->applyNotScottishControllers($conditionBuilder->routingVia('KOK'));
            }
        );

        $this->addAirfieldArrivals(
            'AM',
            self::AMSTERDAM_AIRFIELDS,
            function (ConditionBuilder $conditionBuilder) {
                $this->applyNotScottishControllers(
                    $conditionBuilder->not(function (ConditionBuilder $conditionBuilder) {
                        $conditionBuilder->routingVia('KOK');
                    })
                );
            }
        );
    }

    private function addScottishOnlyExits(): void
    {
        foreach (self::SCOTTISH_ONLY_EXIT_POINTS as $exitPoint) {
            $this->addExitWithExtraConditions(
                $exitPoint['fix'],
                $exitPoint['code'],
                $exitPoint['heading_start'],
                $exitPoint['heading_end'],
                function (ConditionBuilder $conditionBuilder) {
                    $this->applyScottishControllers($conditionBuilder);
                }
            );
        }
    }

    private function addKonanExits(): void
    {
        $this->addExitWithExtraConditions(
            'KONAN',
            'D1',
            self::EAST,
            self::SOUTH_EAST,
            function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->routingVia('KOK');
            }
        );

        $this->addExitWithExtraConditions(
            'KONAN',
            'D',
            self::EAST,
            self::SOUTH_EAST,
            function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->not(function (ConditionBuilder $conditionBuilder) {
                    $conditionBuilder->routingVia('KOK');
                });
            }
        );
    }

    private function addEtratExits(): void
    {
        $this->addExitWithExtraConditions(
            'ETRAT',
            'E3',
            self::SOUTH,
            self::SOUTH,
            function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->arrivalAirfields(['LFRG', 'LFRK', 'LFOH', 'LFOE']);
            }
        );

        $this->addExitWithExtraConditions(
            'ETRAT',
            'E2',
            self::SOUTH,
            self::SOUTH,
            function (ConditionBuilder $conditionBuilder) {
                $conditionBuilder->maximumCruisingLevel(27000);
            }
        );

        $this->addStandardExit('ETRAT', 'E', self::SOUTH, self::SOUTH);
    }

    private function addVeuleExits(): void
    {
        $routes = [
            'TEPRI' => 1,
            'PEKIM' => 2,
            'ODEBU' => 3,
            'KOTAP' => 4,
            'DEKOD' => 5,
        ];

        foreach ($routes as $fix => $routeNumber) {
            $this->addExitWithExtraConditions(
                'XAMAB',
                sprintf('V%d', $routeNumber),
                self::SOUTH,
                self::SOUTH,
                function (ConditionBuilder $conditionBuilder) use ($fix) {
                    $conditionBuilder->cruisingAbove(29000)
                        ->routingVia($fix);
                }
            );

            $this->addExitWithExtraConditions(
                'XAMAB',
                sprintf('X%d', $routeNumber),
                self::SOUTH,
                self::SOUTH,
                function (ConditionBuilder $conditionBuilder) use ($fix) {
                    $conditionBuilder->maximumCruisingLevel(29000)
                        ->routingVia($fix);
                }
            );
        }

        $this->addStandardExit(
            'XAMAB',
            'V',
            self::SOUTH,
            self::SOUTH
        );
    }

    private function addStandardExit(
        string $point,
        string $code,
        array $headingStart,
        array $headingEnd
    ) {
        $this->addExit(
            $code,
            function (ConditionBuilder $conditionBuilder) use ($point, $headingStart, $headingEnd) {
                $conditionBuilder->exitPoint($point, $headingStart[0], $headingEnd[1]);
            },
        );
    }

    private function addExitWithExtraConditions(
        string $point,
        string $code,
        array $headingStart,
        array $headingEnd,
        callable $otherConditions
    ) {
        $this->addExit(
            $code,
            function (ConditionBuilder $conditionBuilder) use (
                $point,
                $headingStart,
                $headingEnd,
                $otherConditions
            ) {
                $otherConditions($conditionBuilder->exitPoint($point, $headingStart[0], $headingEnd[1]));
            },
        );
    }

    private function addExit(
        string $code,
        callable $conditions
    ) {
        IntentionCodeBuilder::begin()
            ->withPriority($this->getPriority())
            ->withCode(function (CodeBuilder $codeBuilder) use ($code) {
                $codeBuilder->singleCode($code);
            })
            ->withCondition(
                function (ConditionBuilder $conditionBuilder) use (
                    $conditions
                ) {
                    $conditions($conditionBuilder);
                }
            )
            ->create();
    }

    private function addAirfieldArrivals(
        string $code,
        array $airfields,
        ?callable $otherConditions = null
    ) {
        if (!$otherConditions) {
            $otherConditions = function (ConditionBuilder $conditionBuilder) {
            };
        }

        IntentionCodeBuilder::begin()
            ->withPriority($this->getPriority())
            ->withCode(function (CodeBuilder $codeBuilder) use ($code) {
                $codeBuilder->singleCode($code);
            })
            ->withCondition(
                function (ConditionBuilder $conditionBuilder) use (
                    $airfields,
                    $otherConditions
                ) {
                    $otherConditions(
                        $conditionBuilder->arrivalAirfields($airfields)
                    );
                }
            )
            ->create();
    }

    private function processDirectionalExits(): void
    {
        $this->addDirectionalExits(self::SOUTH_EAST_EXIT_POINTS, self::SOUTH_EAST, self::SOUTH_EAST);
        $this->addDirectionalExits(self::EAST_SOUTH_EAST_EXIT_POINTS, self::EAST, self::SOUTH_EAST);
        $this->addDirectionalExits(self::EAST_EXIT_POINTS, self::EAST, self::EAST);
        $this->addDirectionalExits(self::EAST_NORTH_EAST_EXIT_POINTS, self::NORTH_EAST, self::EAST);
        $this->addDirectionalExits(self::NORTH_EXIT_POINTS, self::NORTH, self::NORTH);
        $this->addDirectionalExits(self::NORTH_NORTH_WEST_EXIT_POINTS, self::NORTH_WEST, self::NORTH);
        $this->addDirectionalExits(self::WEST_NORTH_WEST_EXIT_POINTS, self::WEST, self::NORTH_WEST);
        $this->addDirectionalExits(self::WEST_SOUTH_WEST_EXIT_POINTS, self::SOUTH_WEST, self::WEST);
        $this->addDirectionalExits(self::WEST_EXIT_POINTS, self::WEST, self::WEST);
        $this->addDirectionalExits(self::SOUTH_EXIT_POINTS, self::SOUTH, self::SOUTH);
    }

    private function addDirectionalExits(array $points, array $headingStart, array $headingEnd): void
    {
        foreach ($points as $exitPoint) {
            $this->addStandardExit($exitPoint['fix'], $exitPoint['code'], $headingStart, $headingEnd);
        }
    }

    private function applyScottishControllers(ConditionBuilder $conditionBuilder): void
    {
        $conditionBuilder->anyOf(function (ConditionBuilder $conditionBuilder) {
            $conditionBuilder->controllerPositionStartWith('SCO')
                ->controllerPositionStartWith('STC')
                ->controllerPositionStartWith('EGP')
                ->controllerPositionStartWith('EGQ')
                ->controllerPositionStartWith('EGA');
        });
    }

    private function applyNotScottishControllers(ConditionBuilder $conditionBuilder): void
    {
        $conditionBuilder->not(function (ConditionBuilder $conditionBuilder) {
            $this->applyScottishControllers($conditionBuilder);
        });
    }

    private function getPriority(): int
    {
        return $this->priority++;
    }
}
