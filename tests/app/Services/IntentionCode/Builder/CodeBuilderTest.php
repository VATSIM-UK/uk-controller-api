<?php

namespace App\Services\IntentionCode\Builder;

use App\BaseUnitTestCase;
use App\Exceptions\IntentionCode\IntentionCodeInvalidException;
use App\Models\IntentionCode\IntentionCode;

class CodeBuilderTest extends BaseUnitTestCase
{
    private CodeBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new CodeBuilder(new IntentionCode());
    }

    public function testItConvertsToArraySingleCodeTwoCharacters()
    {
        $expected = [
            'type' => 'single_code',
            'code' => 'LL',
        ];

        $this->builder->singleCode('LL');
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItConvertsToArraySingleCodeOneCharacter()
    {
        $expected = [
            'type' => 'single_code',
            'code' => 'D',
        ];

        $this->builder->singleCode('D');
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItThrowsExceptionSingleCodeTooShort()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Intention code must be one or two characters');

        $this->builder->singleCode('');
    }

    public function testItThrowsExceptionSingleCodeTooLong()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Intention code must be one or two characters');

        $this->builder->singleCode('LOL');
    }

    public function testItThrowsExceptionSingleCodeIfAlreadySetBySingleCode()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Code is already set for intention code');

        $this->builder->singleCode('KK');
        $this->builder->singleCode('LL');
    }

    public function testItThrowsExceptionSingleCodeIfAlreadySetByAirfieldIdentifier()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Code is already set for intention code');

        $this->builder->airfieldIdentifier();
        $this->builder->singleCode('LL');
    }

    public function testItConvertsToArrayAirfieldIdentifier()
    {
        $expected = [
            'type' => 'airfield_identifier',
        ];
        $this->builder->airfieldIdentifier();
        $this->assertEquals($expected, $this->builder->get());
    }

    public function testItThrowsExceptionAirfieldIdentifierIfAlreadySetBySingleCode()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Code is already set for intention code');

        $this->builder->singleCode('KK');
        $this->builder->airfieldIdentifier();
    }

    public function testItThrowsExceptionAirfieldIdentifierIfAlreadySetByAirfieldIdentifier()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('Code is already set for intention code');

        $this->builder->airfieldIdentifier();
        $this->builder->airfieldIdentifier();
    }

    public function testItThrowsExceptionAirfieldIdentifierIfNothingSet()
    {
        $this->expectException(IntentionCodeInvalidException::class);
        $this->expectExceptionMessage('No code set for this intention code');

        $this->builder->get();
    }

    public function testItLoadsCodeFromExisting()
    {
        $code = IntentionCode::factory()->make();
        $builder = new CodeBuilder($code);

        $this->assertEquals(
            $code->code,
            $builder->get()
        );
    }
}
