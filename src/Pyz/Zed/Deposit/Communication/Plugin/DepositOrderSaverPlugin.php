<?php
/**
 * Durst - project - DepositOrderSaverPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.06.18
 * Time: 10:43
 */

namespace Pyz\Zed\Deposit\Communication\Plugin;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DepositOrderSaverPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin
 * @method DepositFacadeInterface getFacade()
 */
class DepositOrderSaverPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this
            ->getFacade()
            ->saveOrderDeposit($quoteTransfer, $saveOrderTransfer);
    }
}