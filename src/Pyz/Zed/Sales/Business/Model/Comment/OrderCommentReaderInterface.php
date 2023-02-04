<?php
/**
 * Durst - project - OrderCommentReaderInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 14:54
 */

namespace Pyz\Zed\Sales\Business\Model\Comment;

use Generated\Shared\Transfer\OrderDetailsCommentsTransfer;
use \Spryker\Zed\Sales\Business\Model\Comment\OrderCommentReaderInterface as SprykerOrderCommentReaderInterface;

interface OrderCommentReaderInterface extends SprykerOrderCommentReaderInterface
{
    /**
     * Get comments of the type 'customer'
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getCustomerCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer;

    /**
     * Get comments of the type 'merchant'
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getMerchantCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer;
}