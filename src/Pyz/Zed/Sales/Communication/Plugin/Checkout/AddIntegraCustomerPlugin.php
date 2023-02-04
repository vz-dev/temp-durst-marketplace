<?php
/**
 * Durst - project - AddIntegraCustomerPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 11.11.20
 * Time: 09:58
 */

namespace Pyz\Zed\Sales\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Communication\SalesCommunicationFactory;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class AddIntegraCustomerPlugin
 * @package Pyz\Zed\Sales\Communication\Plugin\Checkout
 * @method SalesCommunicationFactory getFactory()
 * @method SalesFacadeInterface getFacade()
 */
class AddIntegraCustomerPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws ContainerKeyNotFoundException
     */
    public function saveOrder(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        if ($this->doesBranchUseIntegra($quoteTransfer->getFkBranch()) !== true) {
            return;
        }

        $integraCredentialsTransfer = $this
            ->getIntegraCredentialsForBranch(
                $quoteTransfer->getFkBranch()
            );

        $customerId = $this
            ->getIntegraCustomerId(
                $quoteTransfer,
                $integraCredentialsTransfer
            );

        $this
            ->getFacade()
            ->saveIntegraCustomerId(
                $saveOrderTransfer,
                $customerId
            );
    }

    /**
     * @param int $idBranch
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    protected function doesBranchUseIntegra(int $idBranch): bool
    {
        return $this
            ->getFactory()
            ->getIntegraFacade()
            ->doesBranchUseIntegra(
                $idBranch
            );
    }

    /**
     * @param int $idBranch
     * @return IntegraCredentialsTransfer
     * @throws ContainerKeyNotFoundException
     */
    protected function getIntegraCredentialsForBranch(int $idBranch): IntegraCredentialsTransfer
    {
        return $this
            ->getFactory()
            ->getIntegraFacade()
            ->getCredentialsByIdBranch(
                $idBranch
            );
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    protected function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): string
    {
        return $this
            ->getFactory()
            ->getCustomerFacade()
            ->getIntegraCustomerId(
                $quoteTransfer,
                $credentialsTransfer
            );
    }
}
