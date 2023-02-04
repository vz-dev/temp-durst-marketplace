<?php
/**
 * Durst - merchant_center - DepositAmountHydratorOrderItemPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-05-07
 * Time: 14:30
 */

namespace Pyz\Zed\Deposit\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class DepositAmountHydratorOrderItemPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin\Sales
 * @method DepositFacadeInterface getFacade()
 */
class DepositAmountHydratorOrderItemPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFacade()
            ->hydrateDepositAmountOrderItem($orderTransfer);
    }
}