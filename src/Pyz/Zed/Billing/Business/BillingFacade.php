<?php

namespace Pyz\Zed\Billing\Business;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method \Pyz\Zed\Billing\Business\BillingBusinessFactory getFactory()
 */
class BillingFacade extends AbstractFacade implements BillingFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     */
    public function createBillingPeriods()
    {
        $this
            ->getFactory()
            ->createBillingPeriodGenerator()
            ->createBillingPeriods();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer|null
     */
    public function getCurrentBillingPeriodForBranchById(int $idBranch): ?BillingPeriodTransfer
    {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->getCurrentBillingPeriodForBranchById($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     */
    public function createBillingItemsForEndedBillingPeriods()
    {
        $this
            ->getFactory()
            ->createBillingItemGenerator()
            ->createBillingItemsForEndedBillingPeriods();
    }

    /**
     * {@inheritDoc}
     *
     */
    public function createBillingItemsForBillingPeriodByBillingPeriodId(int $idBillingPeriod)
    {
        $this
            ->getFactory()
            ->createBillingItemGenerator()
            ->createBillingItemsForBillingPeriodByBillingPeriodId($idBillingPeriod);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBillingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodById(int $idBillingPeriod): BillingPeriodTransfer
    {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->getBillingPeriodById($idBillingPeriod);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBillingPeriod
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByIdAndFkBranch(
        int $idBillingPeriod,
        int $fkBranch
    ): BillingPeriodTransfer {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->getBillingPeriodByIdAndFkBranch($idBillingPeriod, $fkBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBillingPeriod
     * @return \ZipArchive
     */
    public function prepareDownloadForBillingPeriod(int $idBillingPeriod): string
    {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->prepareDownloadForBillingPeriod($idBillingPeriod);
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $time
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByTimeAndBranch(
        DateTime $time,
        int $fkBranch
    ): BillingPeriodTransfer {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->getBillingPeriodByTimeAndBranch(
                $time,
                $fkBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function updateBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): BillingPeriodTransfer
    {
        return $this
            ->getFactory()
            ->createBillingPeriod()
            ->updateBillingPeriod($billingPeriodTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function createBillingPeriodForBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createBillingPeriodGenerator()
            ->createBillingPeriodForBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDatevCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        return $this
            ->getFactory()
            ->createDatevCsvManager()
            ->createDatevCsvForBillingPeriod(
                $billingPeriodTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $fkBranch
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function removeDuplicateEmptyBillingPeriodsForBranch(int $fkBranch): void
    {
        $this
            ->getFactory()
            ->createBillingPeriod()
            ->removeDuplicateEmptyBillingPeriodsForBranch($fkBranch);
    }
}
