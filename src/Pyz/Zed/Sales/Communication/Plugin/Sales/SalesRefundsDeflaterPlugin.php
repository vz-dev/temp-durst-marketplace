<?php
/**
 * Durst - merchant_center - SalesRefundsDeflaterPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-02
 * Time: 07:57
 */

namespace Pyz\Zed\Sales\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Dependency\Plugin\DeflateOrderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class SalesRefundsDeflaterPlugin
 * @package Pyz\Zed\Sales\Communication\Plugin\Sales
 * @method SalesFacadeInterface getFacade()
 */
class SalesRefundsDeflaterPlugin  extends AbstractPlugin implements DeflateOrderPluginInterface
{
    /**
     * Specification:
     *   - Its a plugin which hydrates OrderTransfer when order read is persistence,
     *   - is used to deflate sales refunds from multiple items with quantity 1 -> single item with quantity n
     *
     * @api
     *
     * @param OrderTransfer $orderTransfer
     *
     * @return OrderTransfer
     */
    public function deflate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->deflateSalesRefunds($orderTransfer);
    }

}