<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 07.12.18
 * Time: 00:16
 */

namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Sales\Business\SalesFacadeInterface as SprykerSalesFacadeInterface;

class SalesConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    public const CONST_GRAMM_TO_ADD_TO_ROUND_TO_NEAREST_KG = 500;
    public const CONST_GRAMM_IN_KG = 1000;

    /**
     * @var SprykerSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var TourConfig
     */
    protected $config;

    /**
     * @var int[]
     */
    protected $processIds;

    /**
     * @var int[]
     */
    protected $stateBlacklist;

    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * SalesConcreteTourHydrator constructor.
     * @param SalesFacadeInterface $salesFacade
     * @param int $idBranch
     */
    public function __construct(
        SprykerSalesFacadeInterface $salesFacade,
        TourConfig $config,
        TourQueryContainerInterface $queryContainer
    )
    {
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->salesFacade = $salesFacade;

        $this->processIds = $this->getActiveProcesses();
        $this->stateBlacklist = $this->getStateBlacklist();
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return void
     */
    public function hydrateConcreteTour(
        DstConcreteTour $concreteTourEntity,
        ConcreteTourTransfer $concreteTourTransfer)
    {
        if ($concreteTourEntity->getIdConcreteTour() !== null){
            $concreteTourTransfer->setOrderCount(
                $this->getConcreteTourOrdersCount($concreteTourEntity)
            );
            $concreteTourTransfer->setWeightKg(
                $this->getConcreteTourWeight($concreteTourEntity)
            );
        }
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @return int
     */
    public function getConcreteTourOrdersCount(DstConcreteTour $concreteTourEntity) : int
    {
        $orders = $this->getConcreteTourOrders($concreteTourEntity);
        return count($orders);
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @return int
     */
    protected function getConcreteTourWeight(DstConcreteTour $concreteTourEntity) : int
    {
        $orders = $this->getConcreteTourOrders($concreteTourEntity);

        $tourWeight = 0;

        foreach($orders as $order) {
            $tourWeight += $order
                ->getOrderTotals()
                ->getLast()
                ->getWeightTotal();
        }

        return $this->grammToNearestKgRounded($tourWeight);
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @return SpySalesOrder[]
     */
    protected function getConcreteTourOrders(DstConcreteTour $concreteTourEntity) : array
    {
        $concreteTimeSlots = $concreteTourEntity->getSpyConcreteTimeSlots();

        $orders = [];

        foreach ($concreteTimeSlots as $concreteTimeSlot) {
            foreach($concreteTimeSlot->getSpySalesOrders() as $order) {
                $orders[] = $order;
            }
        }

        return $this->filterConcreteTourOrders($orders);
    }

    /**
     * @param SpySalesOrder[] $orders
     * @return SpySalesOrder[]
     */
    protected function filterConcreteTourOrders(array $orders)
    {
        $filteredOrders = [];

        foreach ($orders as $order) {
            foreach ($order->getItems() as $orderItem) {
                if (in_array($orderItem->getFkOmsOrderProcess(), $this->processIds)) {
                    if ($this->stateBlacklist !== [] && in_array($orderItem->getFkOmsOrderItemState(), $this->stateBlacklist)) {
                        continue;
                    }

                    if (!in_array($order, $filteredOrders)) {
                        $filteredOrders[] = $order;
                    }
                }
            }
        }

        return $filteredOrders;
    }

    /**
     * @return int[]
     */
    protected function getActiveProcesses() : array
    {
        $processes = $this->config->getActiveProcesses();

        $result = $this
            ->queryContainer
            ->querySalesOrderProcessesByName($processes)
            ->find();

        $processIds = [];

        foreach ($result as $row) {
            $processIds[] = $row->getIdOmsOrderProcess();
        }

        return $processIds;
    }

    /**
     * @return int[]
     */
    protected function getStateBlacklist() : array
    {

        $blacklist = $this->config->getStateBlacklist();

        $result = $this
            ->queryContainer
            ->querySalesOrderItemStatesByName($blacklist)
            ->find();

        $blacklist = [];

        foreach ($result as $row) {
            $blacklist[] = $row->getIdOmsOrderItemState();
        }

        return $blacklist;
    }

    /**
     * @param int $gramm
     * @return int
     */
    protected function grammToNearestKgRounded(int $gramm) : int
    {
        return ($gramm + self::CONST_GRAMM_TO_ADD_TO_ROUND_TO_NEAREST_KG) / self::CONST_GRAMM_IN_KG;
    }

}
