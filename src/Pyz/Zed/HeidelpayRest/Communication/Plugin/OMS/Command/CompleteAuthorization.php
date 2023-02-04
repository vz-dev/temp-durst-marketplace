<?php
/**
 * Durst - project - CompleteAuthorization.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.02.19
 * Time: 11:24
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

class CompleteAuthorization extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'HeidelpayRest/CompleteAuthorization';

    /**
     *
     * Command which is executed per order basis
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    )
    {
        return [];
    }
}