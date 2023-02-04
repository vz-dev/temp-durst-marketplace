<?php
/**
 * Durst - project - OrderCommentReader.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 14:54
 */

namespace Pyz\Zed\Sales\Business\Model\Comment;

use Generated\Shared\Transfer\OrderDetailsCommentsTransfer;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use \Spryker\Zed\Sales\Business\Model\Comment\OrderCommentReader as SprykerOrderCommentReader;

class OrderCommentReader extends SprykerOrderCommentReader implements OrderCommentReaderInterface
{
    /**
     * @param \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     */
    public function __construct(SalesQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getCustomerCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer
    {
        $commentsCollection = $this->queryContainer->queryCustomerCommentsByIdSalesOrder($idSalesOrder)->find();

        return $this->hydrateCommentCollectionFromEntityCollection($commentsCollection);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getMerchantCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer
    {
        $commentsCollection = $this->queryContainer->queryMerchantCommentsByIdSalesOrder($idSalesOrder)->find();

        return $this->hydrateCommentCollectionFromEntityCollection($commentsCollection);
    }
}