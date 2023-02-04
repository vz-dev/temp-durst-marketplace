<?php
/**
 * Durst - project - RevertTour.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.09.21
 * Time: 16:22
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class RevertTour
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class RevertTour extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'revertingTour';
    public const NAME = 'CancelOrder/RevertTour';
    public const STATE_NAME = 'revert tour';

    public const TOUCH_CONCRETE_TIMESLOT_TYPE = 'RESOURCE_TYPE_CONCRETE_TIME_SLOT';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $this
            ->getFactory()
            ->getTouchFacade()
            ->touchActive(
                static::TOUCH_CONCRETE_TIMESLOT_TYPE,
                $orderEntity
                    ->getFkConcreteTimeslot()
            );

        return [];
    }
}
