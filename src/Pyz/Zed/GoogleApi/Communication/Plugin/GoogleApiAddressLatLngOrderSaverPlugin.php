<?php
/**
 * Durst - project - GoogleApiAddressLatLngOrderSaverPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 04:58
 */

namespace Pyz\Zed\GoogleApi\Communication\Plugin;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\GoogleApi\Business\GoogleApiFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class GoogleApiAddressLatLngOrderSaverPlugin
 * @package Pyz\Zed\GoogleApi\Communication\Plugin
 * @method GoogleApiFacadeInterface getFacade()
 */
class GoogleApiAddressLatLngOrderSaverPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * Specification:
     * - Retrieves (its) data from the quote object and saves it to the database.
     * - These plugins are already enveloped into a transaction.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     * @api
     *
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $shippingAddress = $quoteTransfer->getShippingAddress();

        if($quoteTransfer->getShippingAddress()->getLat() === null && $quoteTransfer->getShippingAddress()->getLng() === null)
        {
            $this->getFacade()->saveLatLngInOrderAddress($quoteTransfer, $saveOrderTransfer);
        }
    }
}
