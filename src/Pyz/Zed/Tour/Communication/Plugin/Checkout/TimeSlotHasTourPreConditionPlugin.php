<?php
/**
 * Durst - project - TimeSlotHasTourPreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.04.19
 * Time: 13:37
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class TimeSlotHasTourPreConditionPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\Checkout
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class TimeSlotHasTourPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionInterface
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
            ->checkConcreteTimeSlotHasConcreteTour($quoteTransfer, $checkoutResponseTransfer);
    }
}
