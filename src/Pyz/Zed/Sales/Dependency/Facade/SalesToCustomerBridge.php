<?php
/**
 * Durst - project - SalesToCustomerBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 11.11.20
 * Time: 15:26
 */

namespace Pyz\Zed\Sales\Dependency\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Customer\Business\CustomerFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Dependency\Facade\SalesToCustomerBridge as SprykerSalesToCustomerBridge;

class SalesToCustomerBridge extends SprykerSalesToCustomerBridge implements SalesToCustomerInterface
{
    /**
     * @var CustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * SalesToCustomerBridge constructor.
     * @param CustomerFacadeInterface $customerFacade
     */
    public function __construct(
        CustomerFacadeInterface $customerFacade
    )
    {
        $this->customerFacade = $customerFacade;

        parent::__construct($customerFacade);
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string
     */
    public function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): string
    {
        return $this
            ->customerFacade
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
            ->customerFacade
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
     */
    public function generateDurstCustomerReferenceForMerchant(
        int $idBranch,
        CustomerTransfer $customerTransfer
    ): string {
        return $this
            ->customerFacade
            ->generateDurstCustomerReferenceForMerchant(
                $idBranch,
                $customerTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string $customerReference
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function saveDurstCustomerReference(
        SaveOrderTransfer $orderTransfer,
        string $customerReference
    ): void {
        $this
            ->customerFacade
            ->saveDurstCustomerReference(
                $orderTransfer,
                $customerReference
            );
    }
}
