<?php
/**
 * Durst - project - IntegraCustomerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.20
 * Time: 14:04
 */

namespace Pyz\Zed\Sales\Business\Model\Customer;


use Generated\Shared\Transfer\SaveOrderTransfer;

interface IntegraCustomerInterface
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
