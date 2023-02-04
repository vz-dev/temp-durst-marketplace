<?php
/**
 * Durst - project - DeliveryAddressZipCodePreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.06.18
 * Time: 14:33
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryAddressZipCodePreConditionPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class DeliveryAddressZipCodePreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        return $this
            ->getFacade()
            ->checkZipCodesDeliveyAddressTimeSlotMatch($quoteTransfer, $checkoutResponseTransfer);
    }
}