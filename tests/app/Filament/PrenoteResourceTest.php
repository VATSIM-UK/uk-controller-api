<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\Resources\PrenoteResource;
use App\Models\Controller\Prenote;
use Illuminate\Database\Eloquent\Model;

class PrenoteResourceTest extends BaseFilamentTestCase
{
    use ChecksDefaultFilamentAccess;

    protected function getViewEditRecord(): Model
    {
        return Prenote::findOrFail(1);
    }

    protected function getResourceClass(): string
    {
        return PrenoteResource::class;
    }

    protected function getEditText(): string
    {
        return 'Edit Prenote One';
    }

    protected function getCreateText(): string
    {
        return 'Create prenote';
    }

    protected function getViewText(): string
    {
        return 'View Prenote One';
    }

    protected function getIndexText(): array
    {
        return ['Prenotes', 'Prenote One', 'Prenote Two'];
    }
}
