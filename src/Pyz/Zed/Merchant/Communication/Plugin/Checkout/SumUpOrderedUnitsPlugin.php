<?php
/**
 * Durst - project - SumUpOrderedUnitsPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:07
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SumUpOrderedUnitsPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\Checkout
 *
 * @method MerchantFacadeInterface getFacade()
 */
class SumUpOrderedUnitsPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @return void
     */
    public function saveOrder(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        $orderedUnitsCount = 0;

        foreach($quoteTransfer->getItems() as $orderItem){
            $orderedUnitsCount += $orderItem->getQuantity();
        };

        $idBranch = $quoteTransfer->getFkBranch();

        $this
            ->getFacade()
            ->sumUpOrderedUnitsToBranchById($idBranch, $orderedUnitsCount);
    }
}
