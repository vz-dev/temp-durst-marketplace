<?php
/**
 * Durst - project - FailConfirmation.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.07.20
 * Time: 10:38
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * Class FailConfirmation
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method OmsCommunicationFactory getFactory()
 */
class FailConfirmation extends AbstractCommand implements CommandByOrderInterface
{
    use GraphmastersTrait;

    public const NAME = 'WholesaleOrder/FailConfirmation';

    public const TOUCH_CONCRETE_TIMESLOT_TYPE = 'RESOURCE_TYPE_CONCRETE_TIME_SLOT';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     * @return array
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderEntity
                    ->getIdSalesOrder()
            );

        $this
            ->getFactory()
            ->getHeidelpayRestFacade()
            ->cancelPaymentForOrder(
                $orderTransfer
            );

        $this->getFactory()
            ->getDiscountFacade()
            ->resetDiscountVouchers(
                $orderEntity->getDiscounts()
            );

        if($orderEntity->getFkConcreteTimeslot() !== null)
        {
            $this
                ->getFactory()
                ->getTouchFacade()
                ->touchActive(
                    static::TOUCH_CONCRETE_TIMESLOT_TYPE,
                    $orderEntity
                        ->getFkConcreteTimeslot()
                );
        }

        $this->markGraphmastersOrderCancelled($orderEntity);

        return [];
    }
}
