<?php
/**
 * Durst - project - SalesExpenseDepositExpanderPlugin.phphp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-03
 * Time: 11:31
 */

namespace Pyz\Zed\Deposit\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SalesExpenseDepositExpanderPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin\Checkout
 * @method \Pyz\Zed\Deposit\Business\DepositFacadeInterface getFacade()
 */
class SalesExpenseDepositExpanderPlugin extends AbstractPlugin implements CheckoutPreSaveInterface
{
    /**
     * Specification:
     * - Do something before orderTransfer save
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function preSave(QuoteTransfer $quoteTransfer)
    {
        return $this
            ->getFacade()
            ->expandDepositSalesExpenses($quoteTransfer);
    }

}