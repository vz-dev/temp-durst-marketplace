<?php
/**
 * Durst - project - CustomerFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.11.20
 * Time: 16:44
 */

namespace Pyz\Zed\Customer\Business;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface as SprykerCustomerFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface CustomerFacadeInterface extends SprykerCustomerFacadeInterface
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
     *
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
     * @param string $durstCustomerReference
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function saveDurstCustomerReference(
        SaveOrderTransfer $orderTransfer,
        string $durstCustomerReference
    ): void;
}
