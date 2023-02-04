<?php

namespace Pyz\Zed\GraphMasters\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersOrderSavePlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * Specification:
     * - This plugin is called after the order is placed.
     * - Set the success flag to false, if redirect should be headed to an error page afterwords
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     *
     * @api
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        if ($this->getFacade()->doesBranchUseGraphmasters($quoteTransfer->getFkBranch()) !== true) {
            return;
        }

        $graphmastersOrderTransfer = (new GraphMastersOrderTransfer())
            ->setFkOrderReference($saveOrderTransfer->getOrderReference())
            ->setStatus(GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_OPEN);

        $this
            ->getFacade()
            ->saveGraphmastersOrder($graphmastersOrderTransfer);
    }
}
