<?php

namespace App\Policies;

use App\BaseUnitTestCase;

class PluginEditablePolicyTest extends BaseUnitTestCase
{
    private readonly PluginEditableDataPolicy $policy;

    public function setUp(): void
    {
        parent::setUp();
        $this->policy = $this->app->make(PluginEditableDataPolicy::class);
    }

    /**
     * @dataProvider methodProvider
     */
    public function testItChecksAccess(string $method, bool $expected)
    {
        $this->assertEquals($expected, $this->policy->$method());
    }

    public function methodProvider(): array
    {
        return [
            'view' => ['view', true],
            'viewAny' => ['viewAny', true],
            'moveUp' => ['moveUp', true],
            'moveDown' => ['moveDown', true],
            'attach' => ['attach', true],
            'detach' => ['detach', true],
            'update' => ['update', true],
            'create' => ['create', true],
            'delete' => ['delete', true],
            'restore' => ['restore', true],
            'forceDelete' => ['forceDelete', false],
            'detachAny' => ['detachAny', false],
            'dissociate' => ['dissociate', true],
            'dissociateAny' => ['dissociateAny', false],
            'replicate' => ['replicate', true],
            'restoreAny' => ['restoreAny', false],
            'deleteAny' => ['deleteAny', false],
            'forceDeleteAny' => ['forceDeleteAny', false],
        ];
    }
}
