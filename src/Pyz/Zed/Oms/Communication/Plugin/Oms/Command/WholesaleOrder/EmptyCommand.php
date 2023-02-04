<?php
/**
 * Durst - project - EmptyCommand.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 15:40
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

class EmptyCommand extends AbstractPlugin implements CommandByOrderInterface
{
    public const NAME = 'WholesaleOrder/EmptyCommand';

    /**
     * @inheritDoc
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    )
    {
        return $data->getArrayCopy();
    }
}
