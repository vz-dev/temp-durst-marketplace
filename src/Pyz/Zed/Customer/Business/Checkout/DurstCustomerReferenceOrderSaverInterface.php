<?php

namespace Pyz\Zed\Customer\Business\Checkout;

use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface DurstCustomerReferenceOrderSaverInterface
{
    /**
     * Saves the given Durst customer reference to the given order
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string $durstCustomerReference
     * @return void
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function saveDurstCustomerReference(SaveOrderTransfer $orderTransfer, string $durstCustomerReference): void;
}
