<?php
/**
 * Durst - project - SalesClient.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.09.18
 * Time: 15:44
 */

namespace Pyz\Client\Sales;

use Generated\Shared\Transfer\CommentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Client\Sales\SalesClient as SprykerSalesClient;

/**
 * Class SalesClient
 * @package Pyz\Client\Sales
 * @method \Pyz\Client\Sales\SalesFactory getFactory()
 */
class SalesClient extends SprykerSalesClient implements SalesClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\CommentTransfer $commentTransfer
     *
     * @return mixed
     */
    public function addCommentToOrder(CommentTransfer $commentTransfer)
    {
        return $this
            ->getFactory()
            ->createSalesStub()
            ->addCommentToOrder($commentTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createSalesStub()
            ->getOrderByIdSalesOrder($idSalesOrder);
    }
}
