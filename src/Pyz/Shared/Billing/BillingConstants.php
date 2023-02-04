<?php
/**
 * Durst - project - BillingConstants.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 11:45
 */

namespace Pyz\Shared\Billing;


class BillingConstants
{
    // sequence name for billing period needs to be unique for each merchant
    public const BILLING_PERIOD_REFERENCE_SEQUENCE_NAME_FORMAT = 'BILLING-%s';

    public const BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE = 'BILLING_PERIOD_GENERATE_DAYS_IN_ADVANCE';

    // references for all merchants start with this string
    public const BILLING_PERIOD_REFERENCE_PREFIX = 'BILLING_PERIOD_REFERENCE_PREFIX';

    // different parts of billing period reference are separated by this
    public const BILLING_PERIOD_REFERENCE_SEPARATOR = 'BILLING_PERIOD_REFERENCE_SEPARATOR';

    /**
     * In order for zip files to be downloadable they need to be stored temporarily
     */
    public const BILLING_PERIOD_ZIP_ARCHIVE_TEMP_PATH = 'BILLING_PERIOD_ZIP_ARCHIVE_TEMP_PATH';
}
