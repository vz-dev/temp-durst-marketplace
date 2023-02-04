<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-25
 * Time: 10:33
 */

namespace Pyz\Zed\Refund\Persistence;

use Orm\Zed\Refund\Persistence\SpyRefundQuery;
use Spryker\Zed\Refund\Persistence\RefundQueryContainerInterface as SprykerRefundQueryContainerInterface;

interface RefundQueryContainerInterface extends SprykerRefundQueryContainerInterface
{
    /**
     * Return a refund query filtered by the given sales order id
     *
     * @param int $idSalesOrder
     * @return SpyRefundQuery
     */
    public function queryRefundBySalesOrderId(int $idSalesOrder): SpyRefundQuery;
}