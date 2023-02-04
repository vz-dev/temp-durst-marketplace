<?php
/**
 * Durst - project - TourOrder.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */

namespace Pyz\Zed\Tour\Business\Model;

use DateTime;
use Exception;
use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;

/**
 * Class TourOrder
 * @package Pyz\Zed\Tour\Business\Model
 */
class TourOrder implements TourOrderInterface
{
    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface
     */
    protected $omsQueryContainer;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Tour\Business\Model\ConcreteTourInterface
     */
    protected $modelConcreteTour;

    /**
     * @var \Pyz\Zed\Tour\TourConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface
     */
    protected $tourDriverAppMapper;

    /**
     * @var IntegraFacadeInterface
     */
    protected $integraFacade;

    /**
     * @var DiscountQueryContainerInterface
     */
    protected $discountQueryContainer;

    /**
     * TourOrder constructor.
     *
     * @param TourQueryContainerInterface $queryContainer
     * @param OmsQueryContainerInterface $omsQueryContainer
     * @param MerchantFacadeInterface $merchantFacade
     * @param SalesFacadeInterface $salesFacade
     * @param ConcreteTourInterface $modelConcreteTour
     * @param TourConfig $config
     * @param TourDriverappMapperInterface $tourDriverAppMapper
     * @param IntegraFacadeInterface $integraFacade
     * @param DiscountQueryContainerInterface $discountQueryContainer
     */
    public function __construct(
        TourQueryContainerInterface     $queryContainer,
        OmsQueryContainerInterface      $omsQueryContainer,
        MerchantFacadeInterface         $merchantFacade,
        SalesFacadeInterface            $salesFacade,
        ConcreteTourInterface           $modelConcreteTour,
        TourConfig                      $config,
        TourDriverappMapperInterface    $tourDriverAppMapper,
        IntegraFacadeInterface          $integraFacade,
        DiscountQueryContainerInterface $discountQueryContainer
    ) {
        $this->queryContainer = $queryContainer;
        $this->omsQueryContainer = $omsQueryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->salesFacade = $salesFacade;
        $this->modelConcreteTour = $modelConcreteTour;
        $this->config = $config;
        $this->tourDriverAppMapper = $tourDriverAppMapper;
        $this->integraFacade = $integraFacade;
        $this->discountQueryContainer = $discountQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersWithToursByDriver(DriverTransfer $driverTransfer): array
    {
        return $this
            ->getOrdersForDriver($driverTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param DriverTransfer $driverTransfer
     * @return DriverAppTourTransfer[]
     * @throws Exception
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array
    {
        $stateWhiteList = $this->getStateIds();

        $concreteTours = $this
            ->queryContainer
            ->queryToursHydratedForDriverApp(
                $driverTransfer,
                $this->getActiveProcessIds(),
                $stateWhiteList,
                $this->getPastTimeLimit(),
                $this->getFutureTimeLimit($driverTransfer->getFkBranch())
            )
            ->find();

        $distinctTours = $this->removeTourDuplicates($concreteTours);
        $skus = $this->getSkus($distinctTours);

        $this->fetchDiscountsForConcreteTourOrders($distinctTours);

        return $this
            ->tourDriverAppMapper
            ->mapEagerLoadedTourEntitiesToTransfers($distinctTours, $skus, $driverTransfer, $stateWhiteList);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @param int[] $excludedOrderIds
     * @return bool
     */
    public function hasConcreteTourOpenOrdersWithExcludedIds(int $idConcreteTour, array $excludedOrderIds): bool
    {
        return $this
            ->omsQueryContainer
            ->queryOrdersForConcreteTourInState(
                array_keys($this->getProcesses()),
                $this->getStateIds(),
                $idConcreteTour
            )
            ->filterByFkSalesOrder($excludedOrderIds, Criteria::NOT_IN)
            ->count() > 0;
    }

    /**
     * @param iterable|DstConcreteTour[] $concreteTours
     *
     * @return DstConcreteTour[]
     */
    protected function removeTourDuplicates(iterable $concreteTours): array
    {
        $distinctConcreteTours = [];
        foreach ($concreteTours as $concreteTour) {
            $distinctConcreteTours[$concreteTour->getIdConcreteTour()] = $concreteTour;
        }

        return $distinctConcreteTours;
    }

    /**
     * @param iterable|\Orm\Zed\Tour\Persistence\DstConcreteTour[] $concreteTours
     *
     * @return array
     */
    protected function getSkus(iterable $concreteTours): array
    {
        $skus = [];
        foreach ($concreteTours as $concreteTour) {
            foreach ($concreteTour->getSpyConcreteTimeSlots() as $timeSlot) {
                foreach ($timeSlot->getSpySalesOrders() as $order) {
                    foreach ($order->getItems() as $item) {
                        $skus[$item->getSku()] = $item->getSku();
                    }
                }
            }
        }

        return array_keys($skus);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    protected function getPastTimeLimit(): DateTime
    {
        return new DateTime($this->config->getDriverAppTourPastCutOff());
    }

    /**
     * @param int $idBranch
     * @return DateTime
     * @throws Exception
     */
    protected function getFutureTimeLimit(int $idBranch): DateTime
    {
        if($this->integraFacade->doesBranchUseIntegra($idBranch) === true){
            return new DateTime($this->config->getDriverAppTourFutureCutOffIntegra());
        }

        return new DateTime($this->config->getDriverAppTourFutureCutOff());
    }

    /**
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return array
     */
    protected function getOrdersForDriver(DriverTransfer $driverTransfer): array
    {
        $processIds = $this
            ->getProcesses();

        $stateIds = $this
            ->getStateIds();

        $now = new DateTime('+1day midnight');

        $orderItems = $this
            ->omsQueryContainer
            ->queryOrdersForDriverInState(
                array_keys($processIds),
                $stateIds,
                $driverTransfer,
                $now
            )
            ->select([
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR,
            ])
            ->find();

        $idSalesOrders = [];
        $idTours = [];

        foreach ($orderItems as $orderItem) {
            $idSalesOrders[] = $orderItem[SpySalesOrderTableMap::COL_ID_SALES_ORDER];
            $idTours[] = $orderItem[DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR];
        }

        $idSalesOrders = array_unique($idSalesOrders);
        $idTours = array_unique($idTours);

        $orders = [];
        $tours = $this
            ->getToursArray($idTours);

        foreach ($idSalesOrders as $idSalesOrder) {
            $tourOrder = $this
                ->salesFacade
                ->getOrderByIdSalesOrder($idSalesOrder);

            $comments = $this
                ->salesFacade
                ->getOrderCommentsByIdSalesOrder($idSalesOrder)
                ->getComments();

            if (count($comments) > 0) {
                $tourOrder
                    ->setCustomerNote(
                        $comments->offsetGet(0)->getMessage()
                    );
            }

            $tourOrder
                ->getConcreteTimeSlot()
                ->setConcreteTour(
                    $tours[$tourOrder
                            ->getConcreteTimeSlot()
                            ->getFkConcreteTour()]
                );

            $orders[] = $tourOrder;
        }

        return $orders;
    }

    /**
     * @return array
     */
    protected function getStateIds(): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName(
                $this
                ->config
                ->getAcceptedOmsState()
            )
            ->find();

        $states = [];

        foreach ($result as $item) {
            $states[] = $item
                ->getIdOmsOrderItemState();
        }

        return $states;
    }

    /**
     * @return int[]
     */
    protected function getActiveProcessIds(): array
    {
        $activeProcesses = $this
            ->getActiveProcesses();

        $idProcesses = [];

        /** @var \Orm\Zed\Oms\Persistence\SpyOmsOrderProcess $activeProcess */
        foreach ($activeProcesses as $activeProcess) {
            $idProcesses[] = $activeProcess->getIdOmsOrderProcess();
        }

        return $idProcesses;
    }

    /**
     * @return array
     */
    protected function getProcesses(): array
    {
        $activeProcesses = $this
            ->getActiveProcesses();

        $processes = [];

        /* @var $activeProcess \Orm\Zed\Oms\Persistence\SpyOmsOrderProcess */
        foreach ($activeProcesses as $activeProcess) {
            $processes[$activeProcess->getIdOmsOrderProcess()] = $activeProcess->getName();
        }

        return $processes;
    }

    /**
     * @return \Propel\Runtime\Collection\ObjectCollection
     */
    protected function getActiveProcesses(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->getActiveProcesses(
                $this
                ->config
                ->getActiveProcesses()
            )
            ->find();
    }

    /**
     * @param int[] $idTours
     *
     * @return \Generated\Shared\Transfer\ConcreteTourTransfer[]
     */
    protected function getToursArray(array $idTours): array
    {
        return $this
            ->modelConcreteTour
            ->getConcreteToursByIds($idTours);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array
    {
        $orderIds = $this
            ->omsQueryContainer
            ->queryOrdersInStateForConcreteTour(
                array_keys($this->getProcesses()),
                $this->getStateIds(),
                $idConcreteTour
            )
            ->select([
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            ])
            ->find();

        $orderTransfers = [];

        foreach ($orderIds as $orderId) {
            $orderTransfers[] = $this
                ->salesFacade
                ->getOrderByIdSalesOrder($orderId);
        }

        return $orderTransfers;
    }

    /**
     * @param DstConcreteTour[] $concreteTours
     * @throws PropelException
     */
    protected function fetchDiscountsForConcreteTourOrders(array $concreteTours): void
    {
        $orders = [];

        foreach ($concreteTours as $concreteTour) {
            foreach ($concreteTour->getSpyConcreteTimeSlots() as $timeSlot) {
                foreach ($timeSlot->getSpySalesOrders() as $order) {
                    $orders[$order->getIdSalesOrder()] = $order;
                }
            }
        }

        $discounts = $this
            ->discountQueryContainer
            ->queryVoucherDiscountsByOrderIds(array_keys($orders))
            ->find();

        foreach ($discounts as $discount) {
            $orders[$discount->getFkSalesOrder()]->addDiscount($discount);
        }
    }
}
