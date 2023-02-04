<?php
/**
 * Durst - project - ConcreteTimeSlotTouchCheckoutPostSaveHookPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-06
 * Time: 19:45
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPostSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class ConcreteTimeSlotTouchCheckoutPostSaveHookPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class ConcreteTimeSlotTouchCheckoutPostSaveHookPlugin extends AbstractPlugin implements CheckoutPostSaveHookInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $this->getFacade()->runConcreteTimeSlotTouchPostSaveHook($quoteTransfer, $checkoutResponse);
    }
}