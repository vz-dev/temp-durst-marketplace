<?php
/**
 * Durst - project - OrderHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 16:27
 */

namespace Pyz\Zed\Merchant\Business\Sales;


use Generated\Shared\Transfer\OrderTransfer;

interface OrderHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateOrderByBranch(OrderTransfer $orderTransfer): OrderTransfer;
}
