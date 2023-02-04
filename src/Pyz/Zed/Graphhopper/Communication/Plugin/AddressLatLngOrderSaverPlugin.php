<?php
/**
 * Durst - project - AddressLatLngOrderSaverPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-06
 * Time: 08:13
 */

namespace Pyz\Zed\Graphhopper\Communication\Plugin;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Graphhopper\Business\GraphhopperFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class AddressLatLngOrderSaverPlugin
 * @package Pyz\Zed\Graphhopper\Communication\Plugin
 * @method GraphhopperFacadeInterface getFacade()
 */
class AddressLatLngOrderSaverPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * Specification:
     * - Retrieves (its) data from the quote object and saves it to the database.
     * - These plugins are already enveloped into a transaction.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
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
            $this->getFacade()->saveLatLngInOrderAddress($shippingAddress, $saveOrderTransfer);
        }
    }
}
