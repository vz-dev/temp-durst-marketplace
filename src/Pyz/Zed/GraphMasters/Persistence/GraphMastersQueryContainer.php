<?php

namespace Pyz\Zed\GraphMasters\Persistence;

use DateTime;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersCommissioningTimeQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTimeQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrderQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersSettingsQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTimeSlotQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTourQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method GraphMastersPersistenceFactory getFactory()
 */
class GraphMastersQueryContainer extends AbstractQueryContainer implements GraphMastersQueryContainerInterface
{
    /**
     * @return DstGraphmastersSettingsQuery
     */
    public function queryGraphmastersSettings(): DstGraphmastersSettingsQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersSettingsQuery();
    }

    /**
     * @param int $idBranch
     * @return DstGraphmastersSettingsQuery
     * @throws AmbiguousComparisonException
     */
    public function queryGraphMastersSettingsByIdBranch(int $idBranch): DstGraphmastersSettingsQuery
    {
        return $this
            ->queryGraphmastersSettings()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @param int $idSettings
     * @return DstGraphmastersSettingsQuery
     * @throws AmbiguousComparisonException
     */
    public function queryGraphMastersSettingsById(int $idSettings) : DstGraphmastersSettingsQuery
    {
        return $this
            ->queryGraphmastersSettings()
            ->filterByIdGraphmastersSettings($idSettings);
    }

    /**
     * @return DstGraphmastersDeliveryAreaCategoryQuery
     */
    public function queryGraphmastersDeliveryAreaCategory() : DstGraphmastersDeliveryAreaCategoryQuery
    {
        return $this
            ->getFactory()
            ->createGraphmasterDeliveryAreaCategoryQuery();
    }

    /**
     * @return DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery
     */
    public function queryCategoryToDeliveryArea(): DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery
    {
        return $this
            ->getFactory()
            ->createGraphmasterCategoryToDeliveryAreaQuery();
    }

    /**
     * @param int $idCategory
     * @return DstGraphmastersSettingsQuery
     * @throws AmbiguousComparisonException
     */
    public function queryGraphMastersCategoryById(int $idCategory) : DstGraphmastersDeliveryAreaCategoryQuery
    {
        return $this
            ->queryGraphmastersDeliveryAreaCategory()
            ->filterByIdDeliveryAreaCategory($idCategory);
    }

    /**
     * @return DstGraphmastersTimeSlotQuery
     */
    public function createGraphmastersTimeSlotQuery() : DstGraphmastersTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersTimeSlotQuery();
    }

    /**
     * @return DstGraphmastersOpeningTimeQuery
     */
    public function createGraphmastersOpeningTimeQuery(): DstGraphmastersOpeningTimeQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersOpeningTimeQuery();
    }

    /**
     * @return DstGraphmastersCommissioningTimeQuery
     */
    public function createGraphmastersCommissioningTimeQuery(): DstGraphmastersCommissioningTimeQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersCommissioningTimeQuery();
    }

    /**
     * @return DstGraphmastersTourQuery
     */
    public function createGraphmastersTourQuery(): DstGraphmastersTourQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersTourQuery();
    }

    /**
     * @param int $fkBranch
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param string|null $virtualStatus
     * @return DstGraphmastersTourQuery
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function queryToursForPagination(
        int $fkBranch,
        DateTime $startDate = null,
        DateTime $endDate = null,
        string $virtualStatus = null
    ): DstGraphmastersTourQuery {
        $query = $this
            ->getFactory()
            ->createGraphmastersTourQuery()
            ->filterByFkBranch($fkBranch);

        $config = $this->getFactory()->getConfig();

        $earliestAllowedDate = new DateTime($config->getGraphmastersTourFilteringEarliestAllowedDate());

        if ($startDate !== null) {
            if ($startDate <= $earliestAllowedDate) {
                $startDate = $earliestAllowedDate;
            }

            $query->filterByDate($startDate, Criteria::GREATER_EQUAL);
        }

        if ($endDate !== null) {
            $query->filterByDate($endDate, Criteria::LESS_EQUAL);
        }

        $virtualStatusMap = $config->getGraphmastersTourVirtualStatusMap();

        if ($virtualStatus !== null) {
            if ($virtualStatus !== GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_EMPTY) {
                $query->filterByTourStatus($virtualStatusMap[$virtualStatus], Criteria::IN);

                if ($virtualStatus !== GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_IDLE) {
                    $query->filterByOrderCount(0, Criteria::GREATER_THAN);
                }
            } else {
                $query
                    ->filterByTourStatus($virtualStatusMap[GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_ORDERABLE],Criteria::NOT_IN)
                    ->filterByOrderCount(0);
            }

            switch ($virtualStatus) {
                case GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_DELIVERED:
                    $query->orderByDate(Criteria::DESC);
                    break;
                default:
                    $query->orderByDate();
                    break;
            }
        }

        return $query;
    }

    /**
     * @param array $idTours
     * @param string|null $virtualStatus
     * @return DstGraphmastersTourQuery
     */
    public function queryToursForIndex(array $idTours, string $virtualStatus = null): DstGraphmastersTourQuery
    {
        $query = $this
            ->getFactory()
            ->createGraphmastersTourQuery()
            ->filterByIdGraphmastersTour_In($idTours);

        if ($virtualStatus !== null) {
            switch ($virtualStatus) {
                case GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_DELIVERED:
                    $query->orderByDate(Criteria::DESC);
                    break;
                default:
                    $query->orderByDate();
                    break;
            }
        }

        return $query;
    }

    /**
     * @return DstGraphmastersOrderQuery
     */
    public function createGraphmastersOrderQuery(): DstGraphmastersOrderQuery
    {
        return $this
            ->getFactory()
            ->createGraphmastersOrderQuery();
    }

    /**
     * @param DriverTransfer $driverTransfer
     * @param array $processIdWhiteList
     * @param array $stateIdWhiteList
     * @param DateTime $newerThan
     * @param DateTime $olderThan
     * @return DstGraphmastersTourQuery
     */
    public function queryToursHydratedForDriverApp(
        DriverTransfer $driverTransfer,
        array $processIdWhiteList,
        array $stateIdWhiteList,
        DateTime $newerThan,
        DateTime $olderThan
    ): DstGraphmastersTourQuery {
        return $this
            ->createGraphmastersTourQuery()
            // @TODO: Filter by Graphmasters driver
            // ->filterByFkDriver(null, Criteria::ISNULL)
            // ->_or()
            // ->filterByFkDriver($driverTransfer->getIdDriver())
            ->filterByDate($olderThan, Criteria::LESS_THAN)
            ->filterByDate($newerThan, Criteria::GREATER_EQUAL)
            ->filterByFkBranch($driverTransfer->getFkBranch())
            ->joinWithSpyBranch()
            ->joinWithDstGraphmastersOrder()
            ->joinWith('DstGraphmastersOrder.SpySalesOrder')
            ->useDstGraphmastersOrderQuery()
                ->useSpySalesOrderQuery()
                    ->leftJoinWith('SpySalesOrder.Order') // SpySalesOrder.SpySalesPayment is wrongly named SpySalesOrder.Order
                    ->useOrderQuery(null, Criteria::LEFT_JOIN)
                        ->leftJoinWithSalesPaymentMethodType()
                        ->useSalesPaymentMethodTypeQuery(null, Criteria::LEFT_JOIN)
                        ->endUse()
                    ->endUse()
                    ->useShippingAddressQuery('shippingAddress', Criteria::LEFT_JOIN)
                    ->endUse()
                    ->with('shippingAddress')
                    ->useBillingAddressQuery('billingAddress', Criteria::LEFT_JOIN)
                    ->endUse()
                    ->with('billingAddress')
                    ->leftJoinWithItem()
                    ->useItemQuery(null, Criteria::LEFT_JOIN)
                        ->filterByFkOmsOrderProcess($processIdWhiteList, Criteria::IN)
                        ->_or()
                        ->filterByFkOmsOrderProcess(null, Criteria::ISNULL)
                        ->filterByFkOmsOrderItemState($stateIdWhiteList, Criteria::IN)
                        ->_or()
                        ->filterByFkOmsOrderItemState(null, Criteria::ISNULL)
                    ->endUse()
                    ->leftJoinWithOrderComment()
                    ->useOrderCommentQuery(null, Criteria::LEFT_JOIN)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->orderByTourStartEta(Criteria::DESC);
    }

    /**
     * @param int $fkBranch
     * @return DstGraphmastersTourQuery
     * @throws AmbiguousComparisonException
     */
    public function queryTodaysIdleToursByFkBranch(int $fkBranch): DstGraphmastersTourQuery
    {
        return $this
            ->createGraphmastersTourQuery()
            ->filterByDate('today')
            ->filterByTourStatus(GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_IDLE)
            ->filterByFkBranch($fkBranch);
    }
}
