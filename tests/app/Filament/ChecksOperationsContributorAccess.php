<?php

namespace App\Filament;

use App\Filament\AccessCheckingHelpers\ChecksCreateOperationsContributorAccess;
use App\Filament\AccessCheckingHelpers\ChecksEditOperationsContributorAccess;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\AccessCheckingHelpers\ChecksViewFilamentAccess;

/**
 * Rather than repeating the same tests for every class that follows the default Filament access policy,
 * this trait provides the tests and exposes a few methods to customise them.
 */
trait ChecksOperationsContributorAccess
{
    use ChecksCreateOperationsContributorAccess;
    use ChecksEditOperationsContributorAccess;
    use ChecksListingFilamentAccess;
    use ChecksViewFilamentAccess;
}
