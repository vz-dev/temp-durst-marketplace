<?php
/**
 * Durst - project - TourTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.10.19
 * Time: 13:59
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;


use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Tour\Persistence\Base\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\MerchantManagement\MerchantManagementConfig;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;

class TourTable extends AbstractTable
{
    public const HEADER_LABEL_ID = 'Tour-Id';
    public const HEADER_LABEL_TOUR_REF = 'Tournummer / -referenz';
    public const HEADER_LABEL_TOUR_PREP_START = 'Tour Prep Start';
    public const HEADER_LABEL_TOUR_START = 'Tour Start';
    public const HEADER_LABEL_STATUS = 'Status';
    public const HEADER_LABEL_ID_BRANCH = 'Branch ID';
    public const HEADER_LABEL_BRANCH = 'Branch';
    public const HEADER_LABEL_ACTIONS = 'Aktionen';

    public const TABLE_KEY_ACTIONS = 'actions';

    public const TOURTABLE_URL = '/merchant-management/tour/';
    public const TRIGGER_STATE_MACHINE_EVENT_LINK_FORMAT = '<a class="btn-sm btn-primary" href="/state-machine/trigger/trigger-event?event=%s&id-process=%s&identifier=%s&id-state=%s&state-machine-name=%s&redirect=%s">%s</a>';

    protected const DATE_FORMAT = 'd.m.Y, H:i';

    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $tourQueryContainer;

    /**
     * @var StateMachineFacadeInterface
     */
    protected $stateMachineFacade;

    /**
     * @var MerchantManagementConfig
     */
    protected $merchantManagementConfig;

    /**
     * TourTable constructor.
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $tourQueryContainer
     * @param StateMachineFacadeInterface $stateMachineFacade
     * @param MerchantManagementConfig $merchantManagementConfig
     */
    public function __construct(
        \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $tourQueryContainer,
        StateMachineFacadeInterface $stateMachineFacade,
        MerchantManagementConfig $merchantManagementConfig
    )
    {
        $this->tourQueryContainer = $tourQueryContainer;
        $this->stateMachineFacade = $stateMachineFacade;
        $this->merchantManagementConfig = $merchantManagementConfig;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config
            ->setHeader([
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR => self::HEADER_LABEL_ID,
                DstConcreteTourTableMap::COL_TOUR_REFERENCE => self::HEADER_LABEL_TOUR_REF,
                DstConcreteTourTableMap::COL_PREPARATION_START => self::HEADER_LABEL_TOUR_PREP_START,
                DstConcreteTourTableMap::COL_DELIVERY_START => self::HEADER_LABEL_TOUR_START,
                DstConcreteTourTableMap::COL_FK_STATE_MACHINE_ITEM_STATE => self::HEADER_LABEL_STATUS,
                SpyBranchTableMap::COL_ID_BRANCH => self::HEADER_LABEL_ID_BRANCH,
                SpyBranchTableMap::COL_NAME => self::HEADER_LABEL_BRANCH,
                self::TABLE_KEY_ACTIONS => self::HEADER_LABEL_ACTIONS,
            ]);

        $config
            ->setRawColumns([
                self::TABLE_KEY_ACTIONS,
            ]);

        $config
            ->setDefaultSortField(
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR,
                TableConfiguration::SORT_DESC
            );

        $config
            ->setSearchable([
                SpyBranchTableMap::COL_NAME,
                DstConcreteTourTableMap::COL_TOUR_REFERENCE
            ]);

        $config
            ->setSortable([
                DstConcreteTourTableMap::COL_TOUR_REFERENCE,
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR,
                DstConcreteTourTableMap::COL_DELIVERY_START,
                DstConcreteTourTableMap::COL_PREPARATION_START
            ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->tourQueryContainer
            ->queryConcreteTour()
                ->filterByFkStateMachineItemState(null, Criteria::NOT_EQUAL)
            ->joinWithSpyBranch()
            ->joinWithState(Criteria::LEFT_JOIN);

        $queryResults = $this->runQuery($query, $config,true);

        $results = [];
        /** @var \Orm\Zed\Tour\Persistence\DstConcreteTour $tour */
        foreach ($queryResults as $tour) {
            $results[] = [
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR => $tour->getIdConcreteTour(),
                DstConcreteTourTableMap::COL_TOUR_REFERENCE => $tour->getTourReference(),
                DstConcreteTourTableMap::COL_PREPARATION_START => $this->getDateTimeString($tour->getPreparationStart()),
                DstConcreteTourTableMap::COL_DELIVERY_START => $this->getDateTimeString($tour->getDeliveryStart()),
                DstConcreteTourTableMap::COL_FK_STATE_MACHINE_ITEM_STATE => $tour->getState()->getName(),
                SpyBranchTableMap::COL_ID_BRANCH => $tour->getSpyBranch()->getIdBranch(),
                SpyBranchTableMap::COL_NAME => $tour->getSpyBranch()->getName(),
                self::TABLE_KEY_ACTIONS => implode(' ', $this->getAvailableManualTriggers($tour)),
            ];
        }
        return $results;
    }

    /**
     * @param DstConcreteTour $tour
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getAvailableManualTriggers(DstConcreteTour $tour) : array
    {
        $stateMachineIteTransfer = $this->createStateMachineTransfer();

        $stateMachineIteTransfer
            ->setProcessName($tour->getState()->getProcess()->getName())
            ->setStateMachineName($tour->getState()->getProcess()->getStateMachineName())
            ->setStateName($tour->getState()->getName());


        $manualEvents = $this->stateMachineFacade->getManualEventsForStateMachineItem($stateMachineIteTransfer);

        return $this->createTriggerLinksForManualEvents($tour, $manualEvents);
    }

    /**
     * @return StateMachineItemTransfer
     */
    protected function createStateMachineTransfer()
    {
        return new StateMachineItemTransfer();
    }

    /**
     * @param DstConcreteTour $tour
     * @param array $manualEvents
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createTriggerLinksForManualEvents(DstConcreteTour $tour, array $manualEvents) : array
    {
        $triggerLinks = [];

        foreach ($manualEvents as $event){
            $triggerLinks[] = sprintf(self::TRIGGER_STATE_MACHINE_EVENT_LINK_FORMAT,
                rawurlencode($event),
                $tour->getState()->getProcess()->getIdStateMachineProcess(),
                $tour->getIdConcreteTour(),
                $tour->getState()->getIdStateMachineItemState(),
                rawurlencode($tour->getState()->getProcess()->getStateMachineName()),
                self::TOURTABLE_URL,
                $event
            );
        }

        return $triggerLinks;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    protected function getDateTimeString(DateTime $dateTime): string
    {
        $dateTime->setTimezone(
            new DateTimeZone(
                $this->merchantManagementConfig->getProjectTimeZone()
            )
        );

        return $dateTime->format(self::DATE_FORMAT);
    }

}
