<?php
/**
 * Durst - project - TourOrderHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.10.19
 * Time: 11:42
 */

namespace Pyz\Zed\Tour\Business\Sales;


use Generated\Shared\Transfer\OrderTransfer;

interface TourOrderHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateOrderByTourId(OrderTransfer $orderTransfer): OrderTransfer;
}
