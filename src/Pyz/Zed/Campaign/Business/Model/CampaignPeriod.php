<?php
/**
 * Durst - project - CampaignPeriod.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 11:53
 */

namespace Pyz\Zed\Campaign\Business\Model;


use DateInterval;
use DatePeriod;
use DateTime;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriod;
use Orm\Zed\Campaign\Persistence\DstCampaignPeriodQuery;
use Orm\Zed\Campaign\Persistence\Map\DstCampaignPeriodTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodNotFoundException;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;

class CampaignPeriod implements CampaignPeriodInterface
{
    protected const DATEPICKER_FORMAT = 'Y-m-d';

    protected const POSTGRES_DATE_INTERVAL = '(%s - make_interval(days := %s))';

    /**
     * @var \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface[]
     */
    protected $campaignPeriodHydrators;

    /**
     * CampaignPeriod constructor.
     * @param \Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface $queryContainer
     * @param array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface[] $campaignPeriodHydrators
     */
    public function __construct(
        CampaignQueryContainerInterface $queryContainer,
        array $campaignPeriodHydrators
    )
    {
        $this->queryContainer = $queryContainer;
        $this->campaignPeriodHydrators = $campaignPeriodHydrators;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPeriodById(int $idCampaignPeriod): CampaignPeriodTransfer
    {
        $campaignPeriod = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIdCampaignPeriod(
                $idCampaignPeriod
            )
            ->findOne();

        if ($campaignPeriod === null) {
            throw new CampaignPeriodNotFoundException(
                sprintf(
                    CampaignPeriodNotFoundException::MESSAGE,
                    $idCampaignPeriod
                )
            );
        }

        return $this
            ->entityToTransfer(
                $campaignPeriod
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function saveCampaignPeriod(CampaignPeriodTransfer $campaignPeriodTransfer): CampaignPeriodTransfer
    {
        $entity = $this
            ->findCampaignPeriodOrCreate(
                $campaignPeriodTransfer
            );

        $entity
            ->fromArray(
                $campaignPeriodTransfer
                    ->toArray()
            );

        foreach ($campaignPeriodTransfer->getCampaignAdvertisingMaterials() as $campaignAdvertisingMaterial) {
            $material = $this
                ->getCampaignAdvertisingMaterialById(
                    $campaignAdvertisingMaterial
                );

            $entity
                ->addDstCampaignAdvertisingMaterial(
                    $material
                );
        }

        foreach ($this->getUnusedCampaignAdvertisingMaterialForPeriod($campaignPeriodTransfer) as $unusedCampaignAdvertisingMaterial) {
            $entity
                ->removeDstCampaignAdvertisingMaterial(
                    $unusedCampaignAdvertisingMaterial
                );
        }

        $entity
            ->save();

        return $this
            ->entityToTransfer(
                $entity
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function activateCampaignPeriod(int $idCampaignPeriod): bool
    {
        $campaignPeriod = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIdCampaignPeriod(
                $idCampaignPeriod
            )
            ->findOne();

        if ($campaignPeriod === null) {
            throw new CampaignPeriodNotFoundException(
                sprintf(
                    CampaignPeriodNotFoundException::MESSAGE,
                    $idCampaignPeriod
                )
            );
        }

        $rows = $campaignPeriod
            ->setIsActive(
                true
            )
            ->save();

        return ($rows === 1);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignPeriodNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function deactivateCampaignPeriod(int $idCampaignPeriod): bool
    {
        $campaignPeriod = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIdCampaignPeriod(
                $idCampaignPeriod
            )
            ->findOne();

        if ($campaignPeriod === null) {
            throw new CampaignPeriodNotFoundException(
                sprintf(
                    CampaignPeriodNotFoundException::MESSAGE,
                    $idCampaignPeriod
                )
            );
        }

        $rows = $campaignPeriod
            ->setIsActive(
                false
            )
            ->save();

        return ($rows === 1);
    }

    /**
     * @param int|null $idCampaignPeriod
     * @return array|string[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDatesWithCampaigns(
        ?int $idCampaignPeriod
    ): array
    {
        $campaigns = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(
                true
            )
            ->filterByCampaignEndDate(
                new DateTime('now'),
                Criteria::GREATER_THAN
            );

        if ($idCampaignPeriod !== null) {
            $campaigns = $campaigns
                ->filterByIdCampaignPeriod(
                    $idCampaignPeriod,
                    Criteria::NOT_EQUAL
                );
        }

        $campaigns = $campaigns
            ->find();

        $result = [];

        foreach ($campaigns as $campaign) {
            $dates = $this
                ->getDatesInRange(
                    $campaign
                        ->getCampaignStartDate(),
                    $campaign
                        ->getCampaignEndDate()
                );

            foreach ($dates as $date) {
                if (in_array($date, $result) === true) {
                    continue;
                }

                $result[] = $date;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CampaignPeriodTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPeriodList(): array
    {
        // @ToDo: Query muss evtl. für Paginierung angepasst werden
        // damit auch die Vorlaufzeit der Werbemittel einbezogen wird
        $campaignPeriodEntities = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                sprintf(
                    static::POSTGRES_DATE_INTERVAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME
                ) . ' >= ?',
                new DateTime('now')
            )
            ->find();

        $result = [];

        foreach ($campaignPeriodEntities as $campaignPeriodEntity) {
            $campaignPeriod = $this
                ->entityToTransfer(
                    $campaignPeriodEntity
                );

            if ($campaignPeriod->getBookable() !== true) {
                continue;
            }

            $result[] = $campaignPeriod;
        }

        return $result;
    }

    /**
     * @param int $idBranch
     * @return array|CampaignPeriodTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodsForBranch(
        int $idBranch
    ): array
    {
        $ordered = $this
            ->getOrderCampaignIdsForBranch(
                $idBranch
            );

        $campaignPeriodEntities = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                sprintf(
                    static::POSTGRES_DATE_INTERVAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME
                ) . ' >= ?',
                new DateTime('now')
            )
            ->filterByIdCampaignPeriod(
                $ordered,
                Criteria::NOT_IN
            )
            ->filterByIsBookable(true)
            ->orderByCampaignStartDate()
            ->find();

        $result = [];

        foreach ($campaignPeriodEntities as $campaignPeriodEntity) {
            $campaignPeriod = $this
                ->entityToTransfer(
                    $campaignPeriodEntity
                );

            $result[] = $campaignPeriod;
        }

        return $result;
    }

    /**
     * @param int $idBranch
     * @return array|DstCampaignPeriod[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAllAvailableCampaignPeriods(): array
    {
        $campaignPeriodEntities = $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                sprintf(
                    static::POSTGRES_DATE_INTERVAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME
                ) . ' >= ?',
                new DateTime('now')
            )
            ->orderByCampaignStartDate()
            ->find();

        $results = [];

        foreach ($campaignPeriodEntities as $campaignPeriodEntity) {
            $results[] = $campaignPeriodEntity;
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\DstCampaignPeriod $campaignPeriod
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodsForBranchQuery(
        int $idBranch
    ): DstCampaignPeriodQuery
    {
        $ordered = $this
            ->getOrderCampaignIdsForBranch(
                $idBranch
            );

        return $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                sprintf(
                    static::POSTGRES_DATE_INTERVAL,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_START_DATE,
                    DstCampaignPeriodTableMap::COL_CAMPAIGN_LEAD_TIME
                ) . ' >= ?',
                new DateTime('now')
            )
            ->filterByIdCampaignPeriod(
                $ordered,
                Criteria::NOT_IN
            )
            ->orderByCampaignStartDate();
    }

    /**
     * @param int $idBranch
     * @return DstCampaignPeriodQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getAvailableCampaignPeriodsForCampaignIdsQuery(
        array $campaignIds
    ): DstCampaignPeriodQuery
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIsActive(true)
            ->where(
                DstCampaignPeriodTableMap::COL_ID_CAMPAIGN_PERIOD . ' IN ?', $campaignIds
            )
            ->orderByCampaignStartDate();
    }

    /**
     * @param \Orm\Zed\Campaign\Persistence\Base\DstCampaignPeriod $campaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    public function entityToTransfer(DstCampaignPeriod $campaignPeriod): CampaignPeriodTransfer
    {
        $transfer = (new CampaignPeriodTransfer())
            ->fromArray(
                $campaignPeriod
                    ->toArray(),
                true
            );

        foreach ($this->campaignPeriodHydrators as $campaignPeriodHydrator) {
            $campaignPeriodHydrator
                ->hydrateCampaignPeriod(
                    $transfer
                );
        }

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignPeriod
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findCampaignPeriodOrCreate(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): DstCampaignPeriod
    {
        if ($campaignPeriodTransfer->getIdCampaignPeriod() === null) {
            return new DstCampaignPeriod();
        }

        return $this
            ->queryContainer
            ->queryCampaignPeriod()
            ->filterByIdCampaignPeriod(
                $campaignPeriodTransfer
                    ->getIdCampaignPeriod()
            )
            ->findOneOrCreate();
    }

    /**
     * @param int $idCampaignAdvertisingMaterial
     * @return \Orm\Zed\Campaign\Persistence\DstCampaignAdvertisingMaterial
     * @throws \Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getCampaignAdvertisingMaterialById(
        int $idCampaignAdvertisingMaterial
    ): DstCampaignAdvertisingMaterial
    {
        $campaignAdvertisingMaterial = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->filterByIdCampaignAdvertisingMaterial(
                $idCampaignAdvertisingMaterial
            )
            ->findOne();

        if ($campaignAdvertisingMaterial->getIdCampaignAdvertisingMaterial() === null) {
            throw new CampaignAdvertisingMaterialNotFoundException(
                sprintf(
                    CampaignAdvertisingMaterialNotFoundException::MESSAGE_NO_CAMPAIGN_PERIOD,
                    $idCampaignAdvertisingMaterial
                )
            );
        }

        return $campaignAdvertisingMaterial;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getUnusedCampaignAdvertisingMaterialForPeriod(
        CampaignPeriodTransfer $campaignPeriodTransfer
    ): array
    {
        $campaignAdvertisingMaterials = $this
            ->queryContainer
            ->queryCampaignAdvertisingMaterial()
            ->useDstCampaignPeriodCampaignAdvertisingMaterialQuery()
                ->filterByIdCampaignPeriod(
                    $campaignPeriodTransfer
                        ->getIdCampaignPeriod()
                )
                ->filterByIdCampaignAdvertisingMaterial(
                    $campaignPeriodTransfer
                        ->getCampaignAdvertisingMaterials(),
                    Criteria::NOT_IN
                )
            ->endUse()
            ->filterByIsActive(true)
            ->find();

        $result = [];

        foreach ($campaignAdvertisingMaterials as $campaignAdvertisingMaterial) {
            $result[] = $campaignAdvertisingMaterial;
        }

        return $result;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array|string[]
     */
    protected function getDatesInRange(
        DateTime $start,
        DateTime $end
    ): array
    {
        $interval = new DateInterval('P1D');

        $end
            ->add(
                $interval
            );

        $period = new DatePeriod(
            $start,
            $interval,
            $end
        );

        $result = [];

        foreach ($period as $date) {
            $result[] = $date
                ->format(
                    static::DATEPICKER_FORMAT
                );
        }

        return $result;
    }

    /**
     * @param int $idBranch
     * @return array|int[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getOrderCampaignIdsForBranch(
        int $idBranch
    ): array
    {
        $orders = $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrder()
            ->useDstCampaignPeriodQuery()
                ->filterByIsActive(
                    true
                )
            ->endUse()
            ->filterByFkBranch(
                $idBranch
            )
            ->find();

        $result = [];

        foreach ($orders as $order) {
            $result[] = $order
                ->getFkCampaignPeriod();
        }

        return $result;
    }
}
