<?php

namespace App\Filament\AccessCheckingHelpers;

/**
 * This class is specified for clarity about the classes we're using for Filament, but as far
 * as the package is concerned - Management just extends Listing, so access wise we just check
 * for listing.
 */
trait ChecksManageRecordsFilamentAccess
{
    use ChecksListingFilamentAccess;
}
