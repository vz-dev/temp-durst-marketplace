<?php
/**
 * Durst - project - CustomerValidatorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.01.20
 * Time: 14:44
 */

namespace Pyz\Zed\HeidelpayRest\Business\Validation;


use Generated\Shared\Transfer\OrderTransfer;

interface CustomerValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCustomerValid(OrderTransfer $orderTransfer): bool;
}
