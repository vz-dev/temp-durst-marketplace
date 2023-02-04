<?php
/**
 * Durst - project - SalesClientInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.09.18
 * Time: 15:44
 */

namespace Pyz\Client\Sales;

use Generated\Shared\Transfer\CommentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Client\Sales\SalesClientInterface as SprykerSalesClientInterface;

/**
 * Class SalesClientInterface
 * @package Pyz\Client\Sales
 */
interface SalesClientInterface extends SprykerSalesClientInterface
{
    /**
     * @param CommentTransfer $commentTransfer
     * @return mixed
     */
    public function addCommentToOrder(CommentTransfer $commentTransfer);

    /**
     * Specification:
     *  - returns the order with the given id
     *
     * @param int $idSalesOrder
     * @return OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer;
}