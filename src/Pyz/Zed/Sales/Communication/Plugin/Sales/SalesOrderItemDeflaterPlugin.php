<?php
/**
 * Durst - project - SalesOrderItemDeflaterPlugin.phpn.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-11
 * Time: 11:01
 */

namespace Pyz\Zed\Sales\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Dependency\Plugin\DeflateOrderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SalesOrderItemDeflaterPlugin
 * @package Pyz\Zed\Sales\Communication\Plugin\Sales
 * @method SalesFacadeInterface getFacade()
 */
class SalesOrderItemDeflaterPlugin extends AbstractPlugin implements DeflateOrderPluginInterface
{
    /**
     * Specification:
     *   - Its a plugin which hydrates OrderTransfer when order read is persistence,
     *   - is used to deflate sales order items from multiple items with quantity 1 -> single item with quantity n
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function deflate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->deflateSalesOrderItems($orderTransfer);
    }

}