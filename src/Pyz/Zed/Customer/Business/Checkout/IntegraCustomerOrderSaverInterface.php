<?php
/**
 * Durst - project - IntegraCustomerOrderSaverInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.20
 * Time: 13:41
 */

namespace Pyz\Zed\Customer\Business\Checkout;

use Generated\Shared\Transfer\SaveOrderTransfer;

interface IntegraCustomerOrderSaverInterface
{
    /**
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void;
}
