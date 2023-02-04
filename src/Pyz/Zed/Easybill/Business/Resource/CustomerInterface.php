<?php
/**
 * Durst - project - CustomerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.01.20
 * Time: 10:32
 */

namespace Pyz\Zed\Easybill\Business\Resource;


use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerInterface
{
    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return int
     */
    public function createCustomer(CustomerTransfer $customerTransfer): int;
}
