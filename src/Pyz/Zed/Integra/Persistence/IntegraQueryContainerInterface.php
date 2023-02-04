<?php

namespace Pyz\Zed\Integra\Persistence;

use DateTime;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;
use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Orm\Zed\Integra\Persistence\PyzIntegraCredentialsQuery;
use Orm\Zed\Integra\Persistence\PyzIntegraLogQuery;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Orm\Zed\Refund\Persistence\SpyRefundQuery;
use Orm\Zed\Sales\Persistence\SpySalesExpenseQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use PDOStatement;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface IntegraQueryContainerInterface extends QueryContainerInterface
{
    public const VIRTUAL_COL_QUANTITY = 'VirtualColQuantity';
    public const VIRTUAL_COL_HEIDELPAY_SHORT_ID = 'VirtualColHeidelpayShortId';

    /**
     * @param int $idBranch
     *
     * @return SpySalesOrderQuery
     */
    public function queryExportableOrdersForBranch(int $idBranch): SpySalesOrderQuery;

    /**
     * @param int $idBranch
     *
     * @return SpySalesOrderQuery
     */
    public function queryClosableOrdersForBranch(int $idBranch): SpySalesOrderQuery;

    /**
     * @param int[] $idsSalesOrder
     *
     * @return SpySalesExpenseQuery
     */
    public function queryExpensesForOrders(array $idsSalesOrder): SpySalesExpenseQuery;

    /**
     * @param int $idBranch
     *
     * @return SpyRefundQuery
     */
    public function queryRefundsForClosedOrders(int $idBranch): SpyRefundQuery;

    /**
     * @param int[] $idsSalesOrder
     *
     * @return PDOStatement
     */
    public function updateOrdersExported(array $idsSalesOrder): PDOStatement;

    /**
     * @param int[] $idsSalesOrder
     *
     * @return PDOStatement
     */
    public function updateOrdersClosed(array $idsSalesOrder): PDOStatement;

    /**
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentials(): PyzIntegraCredentialsQuery;

    /**
     * @param int $idIntegraCredentials
     *
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentialsById(int $idIntegraCredentials): PyzIntegraCredentialsQuery;

    /**
     * @return PyzIntegraCredentialsQuery
     */
    public function queryActiveIntegraCredentials(): PyzIntegraCredentialsQuery;

    /**
     * @param int $idBranch
     *
     * @return PyzIntegraCredentialsQuery
     */
    public function queryIntegraCredentialsByIdBranch(int $idBranch): PyzIntegraCredentialsQuery;

    /**
     * @return PyzIntegraLogQuery
     */
    public function queryIntegraLog(): PyzIntegraLogQuery;

    /**
     * @param array $references
     *
     * @return SpySalesOrderQuery
     */
    public function queryOrdersByReferences(array $references): SpySalesOrderQuery;

    /**
     * @param string $zipCode
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotByZipCodeBranchAndTime(string $zipCode, int $idBranch, DateTime $start, DateTime $end): SpyConcreteTimeSlotQuery;

    /**
     * @param string $zipCode
     * @param int $idBranch
     *
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotForZipCodeAndBranch(
        string $zipCode,
        int $idBranch
    ): SpyTimeSlotQuery;

    /**
     * @param string $zipCode
     *
     * @return SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByZipCode(string $zipCode): SpyDeliveryAreaQuery;

    /**
     * @param array $tourReferences
     * @param int $idBranch
     *
     * @return DstConcreteTourQuery
     */
    public function queryConcreteToursByReferencesForBranch(array $tourReferences, int $idBranch): DstConcreteTourQuery;

    /**
     * @param int $idBranch
     *
     * @return DstAbstractTourQuery
     */
    public function queryIntegraTour(int $idBranch): DstAbstractTourQuery;

    /**
     * @param int $idBranch
     * @param array $merchantSkus
     *
     * @return MerchantPriceQuery
     */
    public function querySkusForMerchantSkus(int $idBranch, array $merchantSkus): MerchantPriceQuery;

    /**
     * @param array $skus
     *
     * @return SpyDepositQuery
     */
    public function queryDepositForSkus(array $skus): SpyDepositQuery;

    /**
     * @param array $idsConcreteTimeLots
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotsByIdsInArray(array $idsConcreteTimeLots) : SpyConcreteTimeSlotQuery;

    /**
     * @return DstAbstractTourToAbstractTimeSlotQuery
     */
    public function queryAbstractTourToAbstractTimeSlot() : DstAbstractTourToAbstractTimeSlotQuery;

    /**
     * @param string $reference
     * @return SpySalesOrderQuery
     */
    public function queryOrderItemIdsByOrderReferences(string $reference): SpySalesOrderQuery;

    /**
     * @param int $idBranch
     * @return SpySalesOrderQuery
     */
    public function queryIdOrdersForExternalClosableOrdersByBranchId(int $idBranch): SpySalesOrderQuery;
}
