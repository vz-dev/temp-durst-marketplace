<?php
/**
 * Durst - project - BillingPeriodGenerator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:14
 */

namespace Pyz\Zed\Billing\Business\Generator;

use DateInterval;
use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\Billing\Business\Exception\BranchNotFoundException;
use Pyz\Zed\Billing\Business\Model\BillingPeriodInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;

class BillingPeriodGenerator implements BillingPeriodGeneratorInterface
{
    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface
     */
    protected $billingPeriod;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridgeInterface
     */
    protected $billingReferenceGenerator;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridgeInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var string
     */
    protected $daysInAdvanceInterval;

    /**
     * BillingPeriodGenerator constructor.
     *
     * @param \Pyz\Zed\Billing\BillingConfig $config
     * @param \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Billing\Business\Model\BillingPeriodInterface $billingPeriod
     * @param \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface $merchantFacade
     * @param \Pyz\Zed\Billing\Business\Generator\BillingReferenceGeneratorInterface $billingReferenceGenerator
     * @param \Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridgeInterface $merchantQueryContainer
     */
    public function __construct(
        BillingConfig $config,
        BillingQueryContainerInterface $queryContainer,
        BillingPeriodInterface $billingPeriod,
        BillingToMerchantBridgeInterface $merchantFacade,
        BillingReferenceGeneratorInterface $billingReferenceGenerator,
        BillingToMerchantQueryContainerBridgeInterface $merchantQueryContainer
    ) {
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->billingPeriod = $billingPeriod;
        $this->merchantFacade = $merchantFacade;
        $this->billingReferenceGenerator = $billingReferenceGenerator;
        $this->merchantQueryContainer = $merchantQueryContainer;

        $this->daysInAdvanceInterval = $this->getDaysInAdvanceFromConfig();
    }

    /**
     * @return void
     */
    public function createBillingPeriods()
    {
        $this->createNewBillingPeriods();
        $this->createBillingPeriodForEndingPeriods();
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
        $branchEntity = $this->getBranchEntityById($idBranch);

        if ($branchEntity->getBillingStartDate() === null) {
            return;
        }

        try {
            $latestPeriod = $this
                ->billingPeriod
                ->getLatestBillingPeriodForBranch($idBranch);

            $this->createBillingPeriodAfterPeriod($latestPeriod);
        } catch (BillingPeriodEntityNotFoundException $e) {
            $this->createBillingPeriodForBranchEntity($branchEntity);
        }
    }

    /**
     * @param int $idBranch
     * @return \Orm\Zed\Merchant\Persistence\SpyBranch
     */
    protected function getBranchEntityById(int $idBranch): SpyBranch
    {
        $branchEntity = $this
            ->merchantQueryContainer
            ->queryBranch()
            ->findOneByIdBranch($idBranch);

        if ($branchEntity === null) {
            throw BranchNotFoundException::build($idBranch);
        }

        return $branchEntity;
    }

    /**
     * @return void
     */
    protected function createNewBillingPeriods()
    {
        $branchEntities = $this->getBranchesWithInvoiceStartDateAndNoBillingPeriod();

        foreach ($branchEntities as $branchEntity) {
            $this->createBillingPeriodForBranchEntity($branchEntity);
        }
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $branchEntity
     *
     * @return void
     */
    protected function createBillingPeriodForBranchEntity(SpyBranch $branchEntity): void
    {
        $billingPeriodTransfer = $this->createBillingPeriodTransfer();

        $billingPeriodTransfer
            ->setBranch($this->createBranchTransferFromEntity($branchEntity))
            ->setBillingReference($this->billingReferenceGenerator->createBillingReferenceFromBranchId($branchEntity->getIdBranch()))
            ->setStartDate($branchEntity->getBillingStartDate()->format('Y-m-d'))
            ->setEndDate($this->getEndDateFromDateAndIntervalOrEndOfMonth($branchEntity->getBillingStartDate()->format('Y-m-d'), $branchEntity->getBillingCycle(), $branchEntity->getBillingEndOfMonth()));

        $this->billingPeriod->createBillingPeriod($billingPeriodTransfer);
    }

    /**
     * @return void
     */
    protected function createBillingPeriodForEndingPeriods()
    {
        $endingBillingPeriods = $this
            ->billingPeriod
            ->getBillingPeriodsByEndDate($this->getEndDateForDaysinAdvance());

        foreach ($endingBillingPeriods as $endingBillingPeriod) {
            $this->createBillingPeriodAfterPeriod($endingBillingPeriod);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\BillingPeriodTransfer $endingBillingPeriod
     *
     * @return void
     */
    protected function createBillingPeriodAfterPeriod(BillingPeriodTransfer $endingBillingPeriod): void
    {
        $newStartDate = $this->getDateFromStartDateAndInterval($endingBillingPeriod->getEndDate(), '1 day');

        $newBillingPeriodTransfer = $this->createBillingPeriodTransfer();

        $newBillingPeriodTransfer
            ->setBranch($endingBillingPeriod->getBranch())
            ->setBillingReference($this->billingReferenceGenerator->createBillingReferenceFromBranchId($endingBillingPeriod->getBranch()->getIdBranch()))
            ->setStartDate($newStartDate)
            ->setEndDate($this->getEndDateFromDateAndIntervalOrEndOfMonth($newStartDate, $endingBillingPeriod->getBranch()->getBillingCycle(), $endingBillingPeriod->getBranch()->getBillingEndOfMonth()));

        $this->billingPeriod->createBillingPeriod($newBillingPeriodTransfer);
    }

    /**
     * @return iterable
     */
    protected function getBranchesWithInvoiceStartDateAndNoBillingPeriod() : iterable
    {
        return $this
            ->merchantQueryContainer
            ->queryBranch()
            ->filterByBillingStartDate(null, Criteria::NOT_EQUAL)
            ->filterByBillingCycle(null, Criteria::NOT_EQUAL)
            ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->useDstBillingPeriodQuery(null, Criteria::LEFT_JOIN)
                ->filterByBillingReference(null, Criteria::ISNULL)
            ->endUse()
            ->find();
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $branch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    protected function createBranchTransferFromEntity(SpyBranch $branch) : BranchTransfer
    {
        return (new BranchTransfer())
            ->fromArray($branch->toArray(), true);
    }

    /**
     * @return \Generated\Shared\Transfer\BillingPeriodTransfer
     */
    protected function createBillingPeriodTransfer(): BillingPeriodTransfer
    {
        return new BillingPeriodTransfer();
    }

    /**
     * @param string $startDate
     * @param string $interval
     *
     * @return string
     */
    protected function getDateFromStartDateAndInterval(string $startDate, string $interval) : string
    {
        return date('Y-m-d', strtotime($startDate . ' ' . $interval));
    }

    /**
     * @return string
     */
    protected function getEndDateForDaysinAdvance() : string
    {
        return date('Y-m-d', strtotime($this->getDaysInAdvanceFromConfig()));
    }

    /**
     * @return string
     */
    protected function getDaysInAdvanceFromConfig() : string
    {
        return $this
            ->config
            ->getBillingPeriodDaysInAdvance();
    }

    /**
     * @param string $date
     * @param string $cycleString
     * @param bool|null $endOfMonth
     *
     * @return string
     */
    protected function getEndDateFromDateAndIntervalOrEndOfMonth(string $date, string $cycleString, ?bool $endOfMonth) : string
    {
        $endDate = new DateTime($date);
        $endDate->add(DateInterval::createFromDateString($cycleString));

        if ($endOfMonth === true) {
            $endMonthDate = new DateTime($date);
            $endMonthDate->modify('last day of');

            if ($endMonthDate < $endDate) {
                return $endMonthDate->format('Y-m-d');
            }
        }

        return $endDate->format('Y-m-d');
    }
}
