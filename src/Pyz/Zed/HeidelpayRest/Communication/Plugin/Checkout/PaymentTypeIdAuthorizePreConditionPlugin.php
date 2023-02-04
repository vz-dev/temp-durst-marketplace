<?php
/**
 * Durst - project - PaymentTypeIdAuthorizePreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 14:16
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PaymentTypeIdAuthorizePreConditionPlugin
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class PaymentTypeIdAuthorizePreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
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
    )
    {
        return $this
            ->getFacade()
            ->checkPaymentTypeId($quoteTransfer, $checkoutResponseTransfer);
    }
}