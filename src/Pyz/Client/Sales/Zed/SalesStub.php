<?php
/**
 * Durst - project - SalesStub.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.09.18
 * Time: 14:44
 */

namespace Pyz\Client\Sales\Zed;

use Generated\Shared\Transfer\CommentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Client\Sales\Zed\SalesStub as SprykerSalesStub;


class SalesStub extends SprykerSalesStub
{

    public const URL_ADD_COMMENT_TO_ORDER = '/sales/gateway/add-comment';
    protected const URL_GET_ORDER_BY_ID_SALES_ORDER = '/sales/gateway/get-order-by-id-sales-order';

    /**
     * @param CommentTransfer $commentTransfer
     * @return CommentTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function addCommentToOrder(CommentTransfer $commentTransfer)
    {
        $commentTransfer = $this->zedStub->call(static::URL_ADD_COMMENT_TO_ORDER, $commentTransfer);

        return $commentTransfer;
    }

    /**
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer
    {
        $orderTransfer = (new OrderTransfer())
            ->setIdSalesOrder($idSalesOrder);

        /** @var OrderTransfer $orderTransfer */
        $orderTransfer = $this
            ->zedStub
            ->call(
                self::URL_GET_ORDER_BY_ID_SALES_ORDER,
                $orderTransfer
            );

        return $orderTransfer;
    }
}