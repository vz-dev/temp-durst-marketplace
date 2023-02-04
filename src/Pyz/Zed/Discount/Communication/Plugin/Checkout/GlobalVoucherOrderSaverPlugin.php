<?php
/**
 * Durst - project - GlobalVoucherOrderSaverPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.02.21
 * Time: 14:52
 */

namespace Pyz\Zed\Discount\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class GlobalVoucherOrderSaverPlugin
 * @package Pyz\Zed\Discount\Communication\Plugin\Checkout
 * @method \Pyz\Zed\Discount\Business\DiscountFacadeInterface getFacade()
 */
class GlobalVoucherOrderSaverPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws \Throwable
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $this
            ->getFacade()
            ->saveOrderGlobalVoucher(
                $quoteTransfer,
                $saveOrderTransfer
            );
    }
}
