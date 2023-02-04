<?php
/**
 * Durst - project - BranchSupportsPaymentMethodPreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.04.19
 * Time: 14:46
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchSupportsPaymentMethodPreConditionPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\Checkout
 * @method \Pyz\Zed\Merchant\Business\MerchantFacadeInterface getFacade()
 */
class BranchSupportsPaymentMethodPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
{
    /**
     * Specification:
     * - Checks a condition before the order is saved. If the condition fails, an error is added to the response transfer and 'false' is returned.
     * - Check could be passed (returns 'true') along with errors added to the checkout response.
     * - Quote transfer should not be changed
     * - Don't use this plugin to write to a DB
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkCondition(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) {
        return $this
            ->getFacade()
            ->checkBranchSupportsPaymentMethod($quoteTransfer, $checkoutResponseTransfer);
    }
}
