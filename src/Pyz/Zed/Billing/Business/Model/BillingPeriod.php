<?php
/**
 * Durst - project - BillingPeriod.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:21
 */

namespace Pyz\Zed\Billing\Business\Model;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\BillingItemTransfer;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;
use Orm\Zed\Billing\Persistence\DstBillingPeriod;
use Orm\Zed\Billing\Persistence\DstBillingPeriodTaxRateTotal;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class BillingPeriod implements BillingPeriodInterface
{
    public const BILLING_DATE_FORMAT = 'Y-m-d';

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface
     */
    protected $downloadManager;

    /**
     * BillingPeriod constructor.
     *
     * @param \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface $merchantFacade
     * @param \Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface $downloadManager
     */
    public function __construct(
        BillingQueryContainerInterface $queryContainer,
        BillingToMerchantBridgeInterface $merchantFacade,
        DownloadManagerInterface $downloadManager
    ) {
        $this->queryContainer = $queryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->downloadManager = $downloadManager;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function createBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer) : BillingPeriodTransfer
    {
        $this->checkRequirements($billingPeriodTransfer);

        $billingPeriodEntity = $this->transferToEntity($billingPeriodTransfer);
        $billingPeriodEntity->save();

        return $this->entityToTransfer($billingPeriodEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBillingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodById(int $idBillingPeriod) : BillingPeriodTransfer
    {
        $billingPeriodEntity = $this
            ->getBillingPeriodEntityById($idBillingPeriod);

        return $this->entityToTransfer($billingPeriodEntity);
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
        $entity = $this
            ->queryContainer
            ->queryClosedBillingPeriodsByFkBranch($fkBranch)
            ->findOneByIdBillingPeriod($idBillingPeriod);

        if ($entity === null) {
            throw BillingPeriodEntityNotFoundException::createFkBranch(
                $idBillingPeriod,
                $fkBranch
            );
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getLatestBillingPeriodForBranch(int $idBranch): BillingPeriodTransfer
    {
        $billingPeriodEntity = $this
            ->queryContainer
            ->queryLatestBillingPeriodForBranch($idBranch)
            ->findOne();

        if ($billingPeriodEntity === null) {
            throw BillingPeriodEntityNotFoundException::branch($idBranch);
        }

        return $this
            ->entityToTransfer($billingPeriodEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $endDate
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer[]
     */
    public function getBillingPeriodsByEndDate(string $endDate) : array
    {
        $billingPeriodEntities = $this
            ->queryContainer
            ->queryBillingPeriodGetByEndDate($endDate)
            ->find();

        $billingPeriodTransfers = [];

        foreach ($billingPeriodEntities as $billingPeriodEntity) {
            $billingPeriodTransfers[] = $this->entityToTransfer($billingPeriodEntity);
        }

        return $billingPeriodTransfers;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $endDate
     * @param int $branchId
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByEndDateForBranchId(string $endDate, int $branchId) : BillingPeriodTransfer
    {
        $billingPeriodEntity = $this
            ->queryContainer
            ->queryBillingPeriod()
            ->filterByEndDate($endDate)
            ->filterByFkBranch($branchId)
            ->findOne();

        return $this->entityToTransfer($billingPeriodEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer|null
     */
    public function getCurrentBillingPeriodForBranchById(int $idBranch) : ? BillingPeriodTransfer
    {
        $billingPeriodEntity = $this
            ->queryContainer
            ->queryBillingPeriod()
            ->filterByStartDate(date(self::BILLING_DATE_FORMAT), Criteria::LESS_EQUAL)
            ->filterByEndDate(date(self::BILLING_DATE_FORMAT), Criteria::GREATER_EQUAL)
            ->filterByFkBranch($idBranch)
            ->findOne();

        if ($billingPeriodEntity !== null) {
            return $this->entityToTransfer($billingPeriodEntity);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $start
     * @param \DateTime|int $end
     * @param int $fkBranch
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function getBillingPeriodByTimeAndBranch(
        DateTime $time,
        int $fkBranch
    ): BillingPeriodTransfer {

        $billingPeriodEntity = $this
            ->queryContainer
            ->queryBillingPeriodByTimeAndBranch(
                $time,
                $fkBranch
            )
            ->findOne();

        if ($billingPeriodEntity === null) {
            throw BillingPeriodEntityNotFoundException::period(
                $time->format(static::BILLING_DATE_FORMAT),
                $fkBranch
            );
        }

        return $this
            ->entityToTransfer($billingPeriodEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    public function updateBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): BillingPeriodTransfer
    {
        $this->checkRequirements($billingPeriodTransfer);

        $billingPeriodEntity = $this
            ->getBillingPeriodEntityById(
                $billingPeriodTransfer
                    ->requireIdBillingPeriod()
                    ->getIdBillingPeriod()
            );

        $billingPeriodEntity->fromArray($billingPeriodTransfer->toArray());

        if ($billingPeriodEntity->isModified()) {
            $billingPeriodEntity->save();
        }

        $billingPeriodTransfer->setIdBillingPeriod($billingPeriodEntity->getIdBillingPeriod());

        $this->saveTaxRateTotals($billingPeriodEntity, $billingPeriodTransfer);

        return $this->entityToTransfer($billingPeriodEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBillingPeriod
     *
     * @return string
     */
    public function prepareDownloadForBillingPeriod(int $idBillingPeriod): string
    {
        $entity = $this
            ->getBillingPeriodById($idBillingPeriod);

        return $this
            ->downloadManager
            ->prepareDownload($entity);
    }

    /**
     * @param int $fkBranch
     *
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function removeDuplicateEmptyBillingPeriodsForBranch(int $fkBranch): void
    {
        $billingPeriods = $this
            ->queryContainer
            ->queryBillingPeriod()
            ->filterByFkBranch($fkBranch)
            ->find();

        $billingPeriodsToDelete = new ObjectCollection();

        foreach ($billingPeriods as $billingPeriodA) {
            if ($billingPeriodsToDelete->contains($billingPeriodA)) {
                continue;
            }

            foreach ($billingPeriods as $billingPeriodB) {
                if ($billingPeriodA->getIdBillingPeriod() === $billingPeriodB->getIdBillingPeriod() ||
                    $billingPeriodsToDelete->contains($billingPeriodB)
                ) {
                    continue;
                }

                if ($billingPeriodA->getStartDate() == $billingPeriodB->getStartDate() &&
                    $billingPeriodA->getEndDate() == $billingPeriodB->getEndDate() &&
                    ($billingPeriodB->getTotalAmount() === null || $billingPeriodB->getTotalAmount() === 0)
                ) {
                    $billingPeriodsToDelete->append($billingPeriodB);
                }
            }
        }

        foreach ($billingPeriodsToDelete as $billingPeriodToDelete) {
            $billingPeriodToDelete->delete();
        }
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriod
     */
    protected function getBillingPeriodEntityById(int $idBillingPeriod): DstBillingPeriod
    {
        $billingPeriodEntity = $this
            ->queryContainer
            ->queryBillingPeriod()
            ->useDstBillingPeriodTaxRateTotalQuery(null, Criteria::LEFT_JOIN)
            ->endUse()
            ->findOneByIdBillingPeriod($idBillingPeriod);

        if ($billingPeriodEntity === null) {
            throw BillingPeriodEntityNotFoundException::create($idBillingPeriod);
        }

        return $billingPeriodEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    protected function checkRequirements(BillingPeriodTransfer $billingPeriodTransfer): void
    {
        $billingPeriodTransfer
            ->requireBranch()
            ->requireStartDate()
            ->requireEndDate()
            ->requireBillingReference();
    }

    /**
     * @param \Orm\Zed\Billing\Persistence\DstBillingPeriod $billingPeriod
     *
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    protected function entityToTransfer(DstBillingPeriod $billingPeriod) : BillingPeriodTransfer
    {
        $billingPeriodTransfer = ($this->createBillingPeriodTransfer())
            ->fromArray($billingPeriod->toArray(), true)
            ->setBillingItems(new ArrayObject($this->hydrateItems($billingPeriod->getDstBillingItems())))
            ->setBranch($this->getBranchById($billingPeriod->getFkBranch()));

        foreach ($billingPeriod->getDstBillingPeriodTaxRateTotals() as $taxRateTotalEntity) {
            $billingPeriodTransfer->addTaxRateTotals($this->taxRateTotalEntityToTransfer($taxRateTotalEntity));
        }

        return $billingPeriodTransfer;
    }

    /**
     * @param \Orm\Zed\Billing\Persistence\DstBillingPeriodTaxRateTotal $entity
     *
     * @return \Generated\Shared\Transfer\TaxRateTotalTransfer
     */
    protected function taxRateTotalEntityToTransfer(DstBillingPeriodTaxRateTotal $entity): TaxRateTotalTransfer
    {
        return (new TaxRateTotalTransfer())
            ->setRate($entity->getTaxRate())
            ->setAmount($entity->getTaxAmount());
    }

    /**
     * @param iterable|\Orm\Zed\Billing\Persistence\DstBillingItem[] $billingItems
     *
     * @return \Generated\Shared\Transfer\BillingItemTransfer[]
     */
    protected function hydrateItems(iterable $billingItems): array
    {
        $transfers = [];
        foreach ($billingItems as $billingItem) {
            $transfers[] = (new BillingItemTransfer())
                ->fromArray($billingItem->toArray(), true);
        }

        return $transfers;
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return \Orm\Zed\Billing\Persistence\DstBillingPeriod
     */
    protected function transferToEntity(BillingPeriodTransfer $billingPeriodTransfer): DstBillingPeriod
    {
        $entity = new DstBillingPeriod();
        $entity->fromArray($billingPeriodTransfer->toArray());

        $entity->setFkBranch($billingPeriodTransfer->getBranch()->getIdBranch());

        return $entity;
    }

    /**
     * @param DstBillingPeriod $billingPeriodEntity
     * @param BillingPeriodTransfer $billingPeriodTransfer
     *
     * @return void
     */
    protected function saveTaxRateTotals(
        DstBillingPeriod $billingPeriodEntity,
        BillingPeriodTransfer $billingPeriodTransfer
    ): void {
        foreach ($billingPeriodTransfer->getTaxRateTotals() as $taxRateTotalTransfer) {
            foreach ($billingPeriodEntity->getDstBillingPeriodTaxRateTotals() as $existingTaxRateTotalEntity) {
                if ($existingTaxRateTotalEntity->getFkBillingPeriod() === $billingPeriodTransfer->getIdBillingPeriod()
                    && (float)$existingTaxRateTotalEntity->getTaxRate() === (float)$taxRateTotalTransfer->getRate()
                ) {
                    $existingTaxRateTotalEntity
                        ->setTaxAmount($taxRateTotalTransfer->getAmount())
                        ->save();

                    continue 2;
                }
            }

            $taxRateTotalEntity = new DstBillingPeriodTaxRateTotal();
            $taxRateTotalEntity->setTaxAmount($taxRateTotalTransfer->getAmount());
            $taxRateTotalEntity->setTaxRate($taxRateTotalTransfer->getRate());
            $taxRateTotalEntity->setFkBillingPeriod($billingPeriodTransfer->getIdBillingPeriod());

            $taxRateTotalEntity->save();
        }
    }

    /**
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    protected function createBillingPeriodTransfer() : BillingPeriodTransfer
    {
        return new BillingPeriodTransfer();
    }

    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    protected function getBranchById(int $idBranch) : BranchTransfer
    {
        return $this
            ->merchantFacade
            ->getBranchById($idBranch);
    }
}
