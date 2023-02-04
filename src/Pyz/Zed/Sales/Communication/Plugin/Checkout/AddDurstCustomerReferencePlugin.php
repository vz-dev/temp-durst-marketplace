<?php

namespace Pyz\Zed\Sales\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Communication\SalesCommunicationFactory;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method SalesCommunicationFactory getFactory()
 * @method SalesFacadeInterface getFacade()
 */
class AddDurstCustomerReferencePlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function saveOrder(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void {
        $customerReference = $this->generateDurstCustomerReferenceForMerchant(
            $quoteTransfer->getFkBranch(),
            $quoteTransfer->getCustomer()
        );

        $this
            ->getFactory()
            ->getCustomerFacade()
            ->saveDurstCustomerReference(
                $saveOrderTransfer,
                $customerReference
            );
    }

    /**
     * @param int $idBranch
     * @param CustomerTransfer $customerTransfer
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    protected function generateDurstCustomerReferenceForMerchant(
        int $idBranch,
        CustomerTransfer $customerTransfer
    ): string {
        return $this
            ->getFactory()
            ->getCustomerFacade()
            ->generateDurstCustomerReferenceForMerchant($idBranch, $customerTransfer);
    }
}
