<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-25
 * Time: 10:34
 */

namespace Pyz\Zed\Refund\Persistence;

use Orm\Zed\Refund\Persistence\SpyRefundQuery;
use Spryker\Zed\Refund\Persistence\RefundPersistenceFactory;
use Spryker\Zed\Refund\Persistence\RefundQueryContainer as SprykerRefundQueryContainer;

/**
 * Class RefundQueryContainer
 * @package Pyz\Zed\Refund\Persistence
 * @method RefundPersistenceFactory getFactory()
 */
class RefundQueryContainer extends SprykerRefundQueryContainer implements RefundQueryContainerInterface
{

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return SpyRefundQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryRefundBySalesOrderId(int $idSalesOrder): SpyRefundQuery
    {
        return $this
            ->getFactory()
            ->createRefundQuery()
            ->filterByFkSalesOrder($idSalesOrder);
    }
}