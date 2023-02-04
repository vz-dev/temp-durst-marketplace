<?php
/**
 * Durst - project - CustomerFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.11.20
 * Time: 16:45
 */

namespace Pyz\Zed\Customer\Business;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Customer\Business\CustomerFacade as SprykerCustomerFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class CustomerFacade
 * @package Pyz\Zed\Customer\Business
 * @method CustomerBusinessFactory getFactory()
 */
class CustomerFacade extends SprykerCustomerFacade implements CustomerFacadeInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    public function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): string
    {
        return $this
            ->getFactory()
            ->createIntegraCustomerModel()
            ->getIntegraCustomerId(
                $quoteTransfer,
                $credentialsTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void
    {
        $this
            ->getFactory()
            ->createIntegraCustomerOrderSaver()
            ->saveIntegraCustomerId(
                $orderTransfer,
                $customerId
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param CustomerTransfer $customerTransfer
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    public function generateDurstCustomerReferenceForMerchant(
        int $idBranch,
        CustomerTransfer $customerTransfer
    ): string {
        return $this
            ->getFactory()
            ->createDurstCustomerReferenceGenerator()
            ->generateDurstCustomerReferenceForMerchant($idBranch, $customerTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string $durstCustomerReference
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     *
     */
    public function saveDurstCustomerReference(
        SaveOrderTransfer $orderTransfer,
        string $durstCustomerReference
    ): void {
        $this
            ->getFactory()
            ->createDurstCustomerReferenceOrderSaver()
            ->saveDurstCustomerReference(
                $orderTransfer,
                $durstCustomerReference
            );
    }
}
