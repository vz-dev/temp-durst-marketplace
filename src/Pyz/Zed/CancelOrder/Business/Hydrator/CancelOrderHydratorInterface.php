<?php
/**
 * Durst - project - CancelOrderHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:18
 */

namespace Pyz\Zed\CancelOrder\Business\Hydrator;

use Generated\Shared\Transfer\CancelOrderTransfer;

/**
 * Interface CancelOrderHydratorInterface
 * @package Pyz\Zed\CancelOrder\Business\Hydrator
 */
interface CancelOrderHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CancelOrderTransfer $orderTransfer
     */
    public function hydrateCancelOrder(
        CancelOrderTransfer $orderTransfer
    ): void;
}
