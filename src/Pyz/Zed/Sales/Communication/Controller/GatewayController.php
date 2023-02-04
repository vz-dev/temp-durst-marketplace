<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.09.18
 * Time: 16:21
 */

namespace Pyz\Zed\Sales\Communication\Controller;

use Generated\Shared\Transfer\CommentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Sales\Communication\Controller\GatewayController as SprykerGatewayController;

class GatewayController extends SprykerGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\CommentTransfer $commentTransfer
     *
     * @return \Generated\Shared\Transfer\CommentTransfer
     */
    public function addCommentAction(CommentTransfer $commentTransfer): CommentTransfer
    {
        return $this->getFacade()->saveComment($commentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrderAction(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->getOrderByIdSalesOrder($orderTransfer->getIdSalesOrder());
    }
}
