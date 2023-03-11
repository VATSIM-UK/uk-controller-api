<?php

namespace App\Filament;

use App\Filament\AccessCheckingHelpers\ChecksCreateDefaultFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksEditDefaultFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;

/**
 * Rather than repeating the same tests for every class that follows the default Filament access policy,
 * this trait provides the tests and exposes a few methods to customise them.
 */
trait ChecksDefaultFilamentAccess
{
    use ChecksCreateDefaultFilamentAccess;
    use ChecksEditDefaultFilamentAccess;
    use ChecksListingFilamentAccess;
    use ChecksViewFilamentAccess;
}
