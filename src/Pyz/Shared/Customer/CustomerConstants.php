<?php

namespace Pyz\Shared\Customer;

use Spryker\Shared\Customer\CustomerConstants as SprykerCustomerConstants;

interface CustomerConstants extends SprykerCustomerConstants
{
    // sequence name for Durst customer reference needs to be unique for each merchant
    public const DURST_CUSTOMER_REFERENCE_SEQUENCE_NAME_FORMAT = 'DURST_CUSTOMER-%s-%d';
    public const DURST_CUSTOMER_REFERENCE_SEQUENCE_NAME_MERCHANT_PREFIX = 'MER';

    // all Durst customer references are prefixed by this
    public const DURST_CUSTOMER_REFERENCE_PREFIX_FORMAT = 'DCR-%d-';
}
