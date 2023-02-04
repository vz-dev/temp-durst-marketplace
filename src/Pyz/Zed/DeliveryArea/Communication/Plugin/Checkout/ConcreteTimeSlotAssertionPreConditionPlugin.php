<?php
/**
 * Durst - project - ConcreteTimeSlotAssertionPreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.06.18
 * Time: 11:48
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class ConcreteTimeSlotAssertionPreConditionPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class ConcreteTimeSlotAssertionPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        return $this
            ->getFacade()
            ->checkConcreteTimeSlotAssertionsForCheckout($quoteTransfer, $checkoutResponseTransfer);
    }
}