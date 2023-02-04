<?php

namespace Pyz\Zed\Billing\Business;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface BillingFacadeInterface
{
    /**
     * Specification:
     *  - gets current billing period transfer for branch by id
     *  - uses current date to filter start and end-date
     *  - returns null if no current billing period can be found
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer|null
     */
    public function getCurrentBillingPeriodForBranchById(int $idBranch) : ?BillingPeriodTransfer;

    /**
     * Specification:
     *  - gets billing period transfer for billing period with matching id
     *  - if no billing period with the given id can be found an exception will be thrown
     *
     * @param int $idBillingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodById(int $idBillingPeriod): BillingPeriodTransfer;

    /**
     * Specification:
     *  - updates the data of the billing period in the database to match the given transfer
     *  - if no entity with the id set in the transfer can be found an exception
     * @link \Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException will be thrown
     *  - returns the updated version of the billing period
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function updateBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): BillingPeriodTransfer;

    /**
     * Specification:
     *  - gets billing period transfer for billing period with matching id anf branch id
     *  - if no billing period with the given id can be found an exception will be thrown
     *
     * @param int $idBillingPeriod
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByIdAndFkBranch(int $idBillingPeriod, int $fkBranch): BillingPeriodTransfer;

    /**
     * Specification:
     *  - creates new billing periods for branches that have a billing start date and no billing period
     *  - creates new billing periods for ending billing periods
     *  - a billing period consists of start and end date, branch id & billing reference
     *
     * @return void
     */
    public function createBillingPeriods();

    /**
     * Specification:
     *  - creates billing items for ended billing periods
     *  - each billing item contains amount, return value, tax etc. for a sales order
     *
     * @return void
     */
    public function createBillingItemsForEndedBillingPeriods();

    /**
     * Specification:
     *  - loads the billing period with the given id from the database
     *  - if no billing period with the given id can be found an exception will be thrown
     *  - creates a pdf file for with the totals of the billing period
     *  - creates a zip file with all pdf files in the temp folder
     *
     * @see \Pyz\Shared\Billing\BillingConstants::BILLING_PERIOD_ZIP_ARCHIVE_TEMP_PATH
     *  - returns the complete file path
     *
     * @param int $idBillingPeriod
     *
     * @return string
     */
    public function prepareDownloadForBillingPeriod(int $idBillingPeriod): string;

    /**
     * Specification:
     *  - creates billing items for billing period with the passed id
     *  - each billing item contains amount, return value, tax etc. for a sales order
     *
     * @param int $idBillingPeriod
     *
     * @return void
     */
    public function createBillingItemsForBillingPeriodByBillingPeriodId(int $idBillingPeriod);

    /**
     * Specification:
     *  - gets billing period transfer for billing period of given branch that was
     *    active at given time
     *  - if no billing period with the given parameters can be found an exception
     * @link \Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException will be thrown
     *
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
     * Specification:
     *  - creates a new billing period for the branch matching the given id
     *  - the period starts one day after the end date of the latest period
     *    or at the configured start date if this is the first period
     *  - the period ends after the configured billing period duration of the branch
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function createBillingPeriodForBranch(int $idBranch): void;

    /**
     * Specification:
     *  - loads the billing period with the given transfer from the database
     *  - if no billing period with the given transfer can be found an exception will be thrown
     *  - creates a csv file with the totals of the billing period
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    public function createDatevCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string;

    /**
     * Removes all duplicate empty billing periods for the branch with the specified ID
     *
     * @param int $fkBranch
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function removeDuplicateEmptyBillingPeriodsForBranch(int $fkBranch): void;
}
