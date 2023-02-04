<?php
/**
 * Durst - project - DeliveryCostOrderSaverPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.06.18
 * Time: 10:31
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryCostOrderSaverPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class DeliveryCostOrderSaverPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this
            ->getFacade()
            ->saveOrderDeliveryCost($quoteTransfer, $saveOrderTransfer);
    }
}