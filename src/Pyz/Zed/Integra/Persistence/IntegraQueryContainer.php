<?php

namespace Pyz\Zed\Integra\Persistence;

use DateTime;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyConcreteTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;
use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Orm\Zed\HeidelpayRest\Persistence\Map\DstPaymentHeidelpayRestLogTableMap;
use Orm\Zed\Integra\Persistence\PyzIntegraCredentialsQuery;
use Orm\Zed\Integra\Persistence\PyzIntegraLogQuery;
use Orm\Zed\MerchantPrice\Persistence\Map\MerchantPriceTableMap;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Orm\Zed\Oms\Persistence\Map\SpyOmsOrderItemStateTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Refund\Persistence\Map\SpyRefundTableMap;
use Orm\Zed\Refund\Persistence\SpyRefundQuery;
use Orm\Zed\Sales\Persistence\Map\SpySalesExpenseTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderCommentTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderItemTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\SpySalesExpenseQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use PDOStatement;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Integra\IntegraConstants;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method IntegraPersistenceFactory getFactory()
 */
class IntegraQueryContainer extends AbstractQueryContainer implements IntegraQueryContainerInterface
{
    protected const REFUND_DEPOSIT_SKU = 'deposit-';
    protected const EXPENSE_TYPE_RETURNED_DEPOSIT = 'RETURNED_DEPOSIT';
    protected const QUERY_WILDCARD = '%';

    protected const QUERY_HEIDELPAY_SHORT_ID_FORMAT = '(SELECT %s FROM %s WHERE %s = %s AND %s IS NOT NULL ORDER BY %s DESC LIMIT 1)';

    /**
     * @param int $idBranch
     *
     * @return SpySalesOrderQuery
     */
    public function queryExportableOrdersForBranch(int $idBranch): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->withColumn($this->createHeidelpayShortIdQueryString(), static::VIRTUAL_COL_HEIDELPAY_SHORT_ID)
            ->filterByFkBranch($idBranch)
            ->filterByIsExportable(true)
            ->useItemQuery()
                ->withColumn(
                    sprintf(
                        'count(%s)',
                        SpySalesOrderItemTableMap::COL_MERCHANT_SKU
                    ),
                    static::VIRTUAL_COL_QUANTITY
                )
                ->groupByMerchantSku()
                ->groupByFkSalesOrder()
            ->endUse()
            ->useSpyConcreteTimeSlotQuery()
                ->useSpyTimeSlotQuery()
                    ->useSpyDeliveryAreaQuery()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->useOrderCommentQuery(null, Criteria::LEFT_JOIN)
            ->endUse()
            ->orderByIdSalesOrder(Criteria::ASC)
            ->orderBy(SpySalesOrderItemTableMap::COL_MERCHANT_SKU, Criteria::ASC)
            ->select([
                static::VIRTUAL_COL_QUANTITY,
                SpySalesOrderTableMap::COL_INTEGRA_RECEIPT_DID,
                SpySalesOrderTableMap::COL_ORDER_REFERENCE,
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
                SpySalesOrderTableMap::COL_CREATED_AT,
                SpySalesOrderTableMap::COL_INTEGRA_CUSTOMER_NO,
                SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID,
                SpyConcreteTimeSlotTableMap::COL_START_TIME,
                SpyConcreteTimeSlotTableMap::COL_END_TIME,
                SpySalesOrderCommentTableMap::COL_MESSAGE,
                SpySalesOrderItemTableMap::COL_MERCHANT_SKU,
                SpySalesOrderItemTableMap::COL_NAME,
                SpySalesOrderItemTableMap::COL_INTEGRA_POSITION_DID,
                SpyDeliveryAreaTableMap::COL_ZIP_CODE,
                SpyTimeSlotTableMap::COL_INTEGRA_TOUR_NO,
                SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO,
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return SpyRefundQuery
     */
    public function queryRefundsForClosedOrders(int $idBranch): SpyRefundQuery
    {
        return $this
            ->getFactory()
            ->getRefundQueryContainer()
            ->queryRefunds()
            ->useSpySalesOrderQuery()
                ->filterByFkBranch($idBranch)
                ->filterByIsClosable(true)
                ->filterByIsExportable(false)
            ->endUse()
            ->withColumn(
                sprintf(
                    'sum(%s)',
                    SpyRefundTableMap::COL_QUANTITY
                ),
                static::VIRTUAL_COL_QUANTITY
            )
            ->filterBySku(
                sprintf(
                    '%s%s',
                    static::REFUND_DEPOSIT_SKU,
                    static::QUERY_WILDCARD
                ),
                Criteria::NOT_LIKE
            )
            ->groupByFkSalesOrder()
            ->groupByMerchantSku()
            ->select([
                SpyRefundTableMap::COL_FK_SALES_ORDER,
                SpyRefundTableMap::COL_MERCHANT_SKU,
                static::VIRTUAL_COL_QUANTITY,
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return SpySalesOrderQuery
     */
    public function queryClosableOrdersForBranch(int $idBranch): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->withColumn($this->createHeidelpayShortIdQueryString(), static::VIRTUAL_COL_HEIDELPAY_SHORT_ID)
            ->filterByFkBranch($idBranch)
            ->filterByIsExportable(false)
            ->filterByIsClosable(true)
            ->useItemQuery()
                ->withColumn(
                    sprintf(
                        'count(%s)',
                        SpySalesOrderItemTableMap::COL_MERCHANT_SKU
                    ),
                    static::VIRTUAL_COL_QUANTITY
                )
                ->groupByFkOmsOrderItemState()
                ->groupByMerchantSku()
                ->groupByFkSalesOrder()
                ->orderByFkSalesOrder()
                ->orderByMerchantSku()
                ->useStateQuery()
                    //->filterByName_In(['damaged', 'declined', 'missing'])
                ->endUse()
            ->endUse()
            ->useSpyConcreteTimeSlotQuery()
                ->useSpyTimeSlotQuery()
                    ->useSpyDeliveryAreaQuery()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->useOrderCommentQuery(null, Criteria::LEFT_JOIN)
            ->endUse()
            ->orderByIdSalesOrder(Criteria::ASC)
            ->orderBy(SpySalesOrderItemTableMap::COL_MERCHANT_SKU, Criteria::ASC)
            ->select([
                static::VIRTUAL_COL_QUANTITY,
                SpySalesOrderTableMap::COL_INTEGRA_RECEIPT_DID,
                SpySalesOrderTableMap::COL_ORDER_REFERENCE,
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
                SpySalesOrderTableMap::COL_CREATED_AT,
                SpySalesOrderTableMap::COL_INTEGRA_CUSTOMER_NO,
                SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID,
                SpyConcreteTimeSlotTableMap::COL_START_TIME,
                SpyConcreteTimeSlotTableMap::COL_END_TIME,
                SpySalesOrderCommentTableMap::COL_MESSAGE,
                SpySalesOrderItemTableMap::COL_MERCHANT_SKU,
                SpySalesOrderItemTableMap::COL_NAME,
                SpySalesOrderItemTableMap::COL_INTEGRA_POSITION_DID,
                SpyDeliveryAreaTableMap::COL_ZIP_CODE,
                SpyOmsOrderItemStateTableMap::COL_NAME,
                SpyTimeSlotTableMap::COL_INTEGRA_TOUR_NO,
                SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO,
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $idsSalesOrder
     *
     * @return SpySalesExpenseQuery
     */
    public function queryExpensesForOrders(array $idsSalesOrder): SpySalesExpenseQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesExpense()
            ->withColumn($this->createHeidelpayShortIdQueryString(), static::VIRTUAL_COL_HEIDELPAY_SHORT_ID)
            ->useOrderQuery()
                ->useSpyConcreteTimeSlotQuery()
                    ->useSpyTimeSlotQuery()
                        ->useSpyDeliveryAreaQuery()
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->useOrderCommentQuery(null, Criteria::LEFT_JOIN)
                ->endUse()
            ->endUse()
            ->filterByType_Like(
                sprintf(
                    '%s%s',
                    static::EXPENSE_TYPE_RETURNED_DEPOSIT,
                    static::QUERY_WILDCARD
                )
            )
            ->filterByFkSalesOrder_In($idsSalesOrder)
            ->orderBy(SpySalesOrderTableMap::COL_ID_SALES_ORDER, Criteria::ASC)
            ->orderBy(SpySalesExpenseTableMap::COL_ID_SALES_EXPENSE, Criteria::ASC)
            ->select([
                SpySalesOrderTableMap::COL_INTEGRA_RECEIPT_DID,
                SpySalesOrderTableMap::COL_ORDER_REFERENCE,
                SpySalesOrderTableMap::COL_ID_SALES_ORDER,
                SpySalesOrderTableMap::COL_CREATED_AT,
                SpySalesOrderTableMap::COL_INTEGRA_CUSTOMER_NO,
                SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID,
                SpySalesExpenseTableMap::COL_TYPE,
                SpySalesExpenseTableMap::COL_QUANTITY,
                SpySalesExpenseTableMap::COL_MERCHANT_SKU,
                SpySalesExpenseTableMap::COL_NAME,
                SpyConcreteTimeSlotTableMap::COL_START_TIME,
                SpyConcreteTimeSlotTableMap::COL_END_TIME,
                SpySalesOrderCommentTableMap::COL_MESSAGE,
                SpyDeliveryAreaTableMap::COL_ZIP_CODE,
                SpyTimeSlotTableMap::COL_INTEGRA_TOUR_NO,
                SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO,
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $idsSalesOrder
     *
     * @return PDOStatement
     */
    public function updateOrdersExported(array $idsSalesOrder): PDOStatement
    {
        $query = sprintf(
            'UPDATE %s SET is_exportable=false WHERE %s IN (%s)',
            SpySalesOrderTableMap::TABLE_NAME,
            SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            implode(',', $idsSalesOrder)
        );

        return $this
            ->getConnection()
            ->prepare($query);
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $idsSalesOrder
     *
     * @return PDOStatement
     */
    public function updateOrdersClosed(array $idsSalesOrder): PDOStatement
    {
        $query = sprintf(
            'UPDATE %s SET is_closable=false WHERE %s IN (%s);',
            SpySalesOrderTableMap::TABLE_NAME,
            SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            implode(',', $idsSalesOrder)
        );

        return $this
            ->getConnection()
            ->prepare($query);
    }

    /**
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentials(): PyzIntegraCredentialsQuery
    {
        return $this
            ->getFactory()
            ->createIntegraCredentialsQuery();
    }

    /**
     * @return PyzIntegraCredentialsQuery
     */
    public function queryActiveIntegraCredentials(): PyzIntegraCredentialsQuery
    {
        return $this
            ->queryIntegraCredentials()
            ->filterByUseIntegra(true)
            ->filterByFkBranch(null, Criteria::ISNOTNULL)
            ->filterByFtpHost(null, Criteria::ISNOTNULL)
            ->filterByFtpUser(null, Criteria::ISNOTNULL)
            ->filterByFtpPassword(null, Criteria::ISNOTNULL);
    }

    /**
     * @param int $idIntegraCredentials
     *
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentialsById(int $idIntegraCredentials): PyzIntegraCredentialsQuery
    {
        return $this
            ->queryIntegraCredentials()
            ->filterByIdIntegraCredentials($idIntegraCredentials);
    }

    /**
     * @param int $idBranch
     *
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentialsByIdBranch(int $idBranch): PyzIntegraCredentialsQuery
    {
        return $this
            ->queryIntegraCredentials()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @return PyzIntegraLogQuery
     */
    public function queryIntegraLog(): PyzIntegraLogQuery
    {
        return $this
            ->getFactory()
            ->createIntegraLogQuery();
    }

    /**
     * @param array $references
     *
     * @return SpySalesOrderQuery
     */
    public function queryOrdersByReferences(array $references): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->filterByOrderReference_In($references);
    }

    /**
     * @param array $tourReferences
     * @param int $idBranch
     *
     * @return DstConcreteTourQuery
     */
    public function queryConcreteToursByReferencesForBranch(array $tourReferences, int $idBranch): DstConcreteTourQuery
    {
        return $this
            ->getFactory()
            ->getTourQueryContainer()
            ->queryConcreteTour()
            ->filterByTourReference_In($tourReferences)
            ->filterByFkBranch($idBranch)
            ->select([
                DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR,
                DstConcreteTourTableMap::COL_TOUR_REFERENCE,
            ]);
    }

    /**
     * @param int $idBranch
     *
     * @return DstAbstractTourQuery
     */
    public function queryIntegraTour(int $idBranch): DstAbstractTourQuery
    {
        return $this
            ->getFactory()
            ->getTourQueryContainer()
            ->queryAbstractTour()
            ->filterByFkBranch($idBranch)
            ->filterByName(IntegraConstants::INTEGRA_TOUR_NAME)
            ->select(DstAbstractTourTableMap::COL_ID_ABSTRACT_TOUR);
    }

    /**
     * @param string $zipCode
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotByZipCodeBranchAndTime(string $zipCode, int $idBranch, DateTime $start, DateTime $end): SpyConcreteTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->getDeliveryAreaQueryContainer()
            ->queryConcreteTimeSlot()
            ->useSpyTimeSlotQuery()
                ->useSpyDeliveryAreaQuery()
                    ->filterByZipCode($zipCode)
                ->endUse()
                ->filterByFkBranch($idBranch)
            ->endUse()
            ->filterByStartTime($start)
            ->filterByEndTime($end);
    }

    /**
     * @param string $zipCode
     * @param int $idBranch
     *
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotForZipCodeAndBranch(
        string $zipCode,
        int $idBranch
    ): SpyTimeSlotQuery {
        return $this
            ->getFactory()
            ->getDeliveryAreaQueryContainer()
            ->queryTimeSlot()
            ->useSpyDeliveryAreaQuery()
                ->filterByZipCode($zipCode)
            ->endUse()
            ->filterByFkBranch($idBranch);
    }

    /**
     * @param string $zipCode
     *
     * @return SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByZipCode(string $zipCode): SpyDeliveryAreaQuery
    {
        return $this
            ->getFactory()
            ->getDeliveryAreaQueryContainer()
            ->queryDeliveryArea()
            ->filterByZipCode($zipCode);
    }

    /**
     * @param int $idBranch
     * @param array $merchantSkus
     *
     * @return MerchantPriceQuery
     */
    public function querySkusForMerchantSkus(int $idBranch, array $merchantSkus): MerchantPriceQuery
    {
        return $this
            ->getFactory()
            ->getMerchantPriceQueryContainer()
            ->queryPrices()
            ->useSpyProductQuery()
            ->endUse()
            ->filterByFkBranch($idBranch)
            ->filterByMerchantSku_In($merchantSkus)
            ->select([
                SpyProductTableMap::COL_SKU,
                MerchantPriceTableMap::COL_MERCHANT_SKU,
            ]);
    }

    /**
     * @param array $skus
     *
     * @return SpyDepositQuery
     */
    public function queryDepositForSkus(array $skus): SpyDepositQuery
    {
        return $this
            ->getFactory()
            ->getDepositQueryContainer()
            ->queryDeposit()
            ->useSpyProductQuery()
                ->filterBySku_In($skus)
            ->endUse();
    }

    protected function createHeidelpayShortIdQueryString() : string
    {
        return sprintf(
            static::QUERY_HEIDELPAY_SHORT_ID_FORMAT,
            DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID,
            DstPaymentHeidelpayRestLogTableMap::TABLE_NAME,
            DstPaymentHeidelpayRestLogTableMap::COL_FK_SALES_ORDER,
            SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID,
            DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT
        );
    }

    /**
     * @param array $idsConcreteTimeLots
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotsByIdsInArray(array $idsConcreteTimeLots) : SpyConcreteTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->getDeliveryAreaQueryContainer()
            ->queryConcreteTimeSlot()
            ->filterByIdConcreteTimeSlot_In($idsConcreteTimeLots);
    }

    /**
     * @return DstAbstractTourToAbstractTimeSlotQuery
     */
    public function queryAbstractTourToAbstractTimeSlot() : DstAbstractTourToAbstractTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->getTourQueryContainer()
            ->queryAbstractTourToAbstractTimeSlot();
    }

    /**
     * @param string $reference
     * @return SpySalesOrderQuery
     * @throws AmbiguousComparisonException
     */
    public function queryOrderItemIdsByOrderReferences(string $reference): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->useItemQuery()
            ->endUse()
            ->filterByOrderReference($reference)
            ->select(
                SpySalesOrderItemTableMap::COL_ID_SALES_ORDER_ITEM
            );
    }

    /**
     * @param int $idBranch
     * @return SpySalesOrderQuery
     * @throws AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function queryIdOrdersForExternalClosableOrdersByBranchId(int $idBranch): SpySalesOrderQuery
    {
        return $this
            ->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrder()
            ->filterByFkBranch($idBranch)
            ->filterByIsClosable(true)
            ->filterByIsExternal(true)
            ->select(
                SpySalesOrderTableMap::COL_ID_SALES_ORDER
            );
    }
}
