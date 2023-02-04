<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 22.01.18
 * Time: 17:34
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByItemInterface;

class DeclineOrderCommand extends AbstractCommand implements CommandByItemInterface
{

    /**
     *
     * Command which is executed per order item basis
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(SpySalesOrderItem $orderItem, ReadOnlyArrayObject $data)
    {
        return [];
    }
}