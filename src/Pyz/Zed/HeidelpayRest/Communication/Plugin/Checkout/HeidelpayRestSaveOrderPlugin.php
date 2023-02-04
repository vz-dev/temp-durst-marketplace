<?php
/**
 * Durst - project - HeidelpaySaveOrderPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 09:23
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class HeidelpayRestSaveOrderPlugin
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout
 * @method \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface getFacade()
 */
class HeidelpayRestSaveOrderPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{

    /**
     * Specification:
     * - Retrieves (its) data from the quote object and saves it to the database.
     * - These plugins are already enveloped into a transaction.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrder(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    )
    {
        $this
            ->getFacade()
            ->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }
}