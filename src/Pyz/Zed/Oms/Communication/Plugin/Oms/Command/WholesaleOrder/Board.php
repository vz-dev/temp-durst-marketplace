<?php
/**
 * Durst - project - Board.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.07.18
 * Time: 15:19
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByItemInterface;

class Board extends AbstractCommand implements CommandByItemInterface
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