<?php
/**
 * Durst - project - BillingPeriodInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:21
 */

namespace Pyz\Zed\Billing\Business\Model;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface BillingPeriodInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function createBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer) : BillingPeriodTransfer;

    /**
     * @param int $idBillingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodById(int $idBillingPeriod) : BillingPeriodTransfer;

    /**
     * @param int $idBillingPeriod
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByIdAndFkBranch(
        int $idBillingPeriod,
        int $fkBranch
    ): BillingPeriodTransfer;

    /**
     * @param string $endDate
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer[]
     */
    public function getBillingPeriodsByEndDate(string $endDate) : array;

    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getLatestBillingPeriodForBranch(int $idBranch): BillingPeriodTransfer;

    /**
     * @param string $endDate
     * @param int $branchId
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByEndDateForBranchId(string $endDate, int $branchId) : BillingPeriodTransfer;

    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer|null
     */
    public function getCurrentBillingPeriodForBranchById(int $idBranch) : ?BillingPeriodTransfer;

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function updateBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): BillingPeriodTransfer;

    /**
     * @param int $idBillingPeriod
     *
     * @return string
     */
    public function prepareDownloadForBillingPeriod(int $idBillingPeriod): string;

    /**
     * @param \DateTime $time
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByTimeAndBranch(
        DateTime $time,
        int $fkBranch
    ): BillingPeriodTransfer;

    /**
     * @param int $fkBranch
     *
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function removeDuplicateEmptyBillingPeriodsForBranch(int $fkBranch): void;
}
