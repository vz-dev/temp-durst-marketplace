<?php
/**
 * Durst - project - SalesToCalculationInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 11.11.20
 * Time: 15:27
 */

namespace Pyz\Zed\Sales\Dependency\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Dependency\Facade\SalesToCustomerInterface as SprykerSalesToCustomerInterface;

interface SalesToCustomerInterface extends SprykerSalesToCustomerInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string
     */
    public function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): string;

    /**
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void;

    /**
     * Generates a merchant-specific Durst customer reference based on a given branch ID and customer
     *
     * @param int $idBranch
     * @param CustomerTransfer $customerTransfer
     * @return string
     */
    public function generateDurstCustomerReferenceForMerchant(
        int $idBranch,
        CustomerTransfer $customerTransfer
    ): string;

    /**
     * Saves the given Durst customer reference to the given order
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
    ): void;
}
