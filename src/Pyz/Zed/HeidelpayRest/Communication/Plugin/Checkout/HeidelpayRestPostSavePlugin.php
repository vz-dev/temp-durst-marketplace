<?php
/**
 * Durst - project - HeidelpayPostSavePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 09:24
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPostSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class HeidelpayRestPostSavePlugin
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class HeidelpayRestPostSavePlugin extends AbstractPlugin implements CheckoutPostSaveHookInterface
{

    /**
     * Specification:
     * - This plugin is called after the order is placed.
     * - Set the success flag to false, if redirect should be headed to an error page afterwords
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function executeHook(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponse
    )
    {
        $this
            ->getFacade()
            ->postSaveHook($quoteTransfer, $checkoutResponse);
    }
}