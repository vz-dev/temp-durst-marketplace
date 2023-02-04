<?php
/**
 * Durst - project - GraphMastersImportOrderPostCheckoutPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.05.21
 * Time: 14:34
 */

namespace Pyz\Zed\GraphMasters\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPostSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class GraphMastersImportOrderPostCheckoutPlugin
 * @package Pyz\Zed\GraphMasters\Communication\Plugin\Checkout
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersImportOrderPostCheckoutPlugin extends AbstractPlugin implements CheckoutPostSaveHookInterface
{
    /**
     * Specification:
     * - This plugin is called after the order is placed.
     * - Set the success flag to false, if redirect should be headed to an error page afterwords
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @api
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $orderReference = $checkoutResponse->getSaveOrder()->getOrderReference();

        if ($this->getFacade()->doesBranchUseGraphmasters($quoteTransfer->getFkBranch()) !== true
            || $this->getFacade()->isOrderMarkedCancelled($orderReference) === true
        ) {
            return;
        }

        $this
            ->getFacade()
            ->importOrder($quoteTransfer, $checkoutResponse->getSaveOrder()->getIdSalesOrder());
    }
}
