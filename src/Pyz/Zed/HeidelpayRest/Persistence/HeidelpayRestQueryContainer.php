<?php
/**
 * Durst - project - HeidelpayRestQueryContainer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 10:27
 */

namespace Pyz\Zed\HeidelpayRest\Persistence;

use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class HeidelpayRestQueryContainer
 * @package Pyz\Zed\HeidelpayRest\Persistence
 * @method \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestPersistenceFactory getFactory()
 */
class HeidelpayRestQueryContainer extends AbstractQueryContainer implements HeidelpayRestQueryContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): DstPaymentHeidelpayRestQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentQuery()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * @param string $orderRef
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentBySalesOrderRef(string $orderRef): DstPaymentHeidelpayRestQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentQuery()
            ->useSpySalesOrderQuery()
                ->filterByOrderReference($orderRef)
            ->endUse();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrder(int $idSalesOrder): DstPaymentHeidelpayRestLogQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestLogQuery()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSlesOrder
     * @param string $type
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrderAndType(
        int $idSlesOrder,
        string $type
    ): DstPaymentHeidelpayRestLogQuery {
        return $this
            ->getFactory()
            ->createHeidelpayRestLogQuery()
            ->filterByFkSalesOrder($idSlesOrder)
            ->filterByTransactionType($type);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentHeidelpayRest
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByIdPaymentHeidelpayRest(int $idPaymentHeidelpayRest): DstPaymentHeidelpayRestQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentQuery()
            ->filterByIdPaymentHeidelpayRest($idPaymentHeidelpayRest);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     * @param array $status
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrderTypeAndStatuses(
        int $idSalesOrder,
        string $transactionType,
        array $status
    ): DstPaymentHeidelpayRestLogQuery {
        return $this
            ->getFactory()
            ->createHeidelpayRestLogQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->filterByTransactionType($transactionType)
            ->filterByStatus($status, Criteria::IN)
            ->orderByCreatedAt(Criteria::DESC);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLog(): DstPaymentHeidelpayRestLogQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestLogQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $paymentId
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByPaymentId(string $paymentId): DstPaymentHeidelpayRestQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestPaymentQuery()
            ->filterByPaymentId($paymentId);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string[] $transactionTypes
     * @param string $pending
     * @param string $error
     * @param array $recoverableErrors
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
        int $idSalesOrder,
        array $transactionTypes,
        string $pending,
        string $error,
        array $recoverableErrors
    ): DstPaymentHeidelpayRestLogQuery
    {
        return $this
            ->getFactory()
            ->createHeidelpayRestLogQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->filterByTransactionType_In($transactionTypes)
            ->condition('error', 'DstPaymentHeidelpayRestLog.Status = ?', $error)
            ->condition('recoverable', 'DstPaymentHeidelpayRestLog.ErrorCode IN ?', $recoverableErrors)
            ->combine(['error', 'recoverable'], 'and', 'recoverableErrors')
            ->condition('pending', 'DstPaymentHeidelpayRestLog.Status = ?', $pending)
            ->where(['recoverableErrors', 'pending'], 'or')
            ->orderByCreatedAt(Criteria::DESC);
    }
}
