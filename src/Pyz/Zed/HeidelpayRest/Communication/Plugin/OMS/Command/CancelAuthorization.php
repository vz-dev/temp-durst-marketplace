<?php
/**
 * Durst - project - CancelAuthorization.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 14:09
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command;


use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\HeidelpayRest\Communication\HeidelpayRestCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

/**
 * Class CancelAuthorization
 * @package Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command
 * @method HeidelpayRestFacadeInterface getFacade()
 * @method HeidelpayRestCommunicationFactory getFactory()
 *
 */
class CancelAuthorization extends AbstractPlugin implements CommandByOrderInterface
{
    use GraphmastersTrait;

    public const NAME = 'HeidelpayRest/CancelAuthorization';

    public const TOUCH_CONCRETE_TIMESLOT_TYPE = 'RESOURCE_TYPE_CONCRETE_TIME_SLOT';

    /**
     * Command which is executed per order basis
     *
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     *
     * @return array
     *
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ) {
        $this
            ->getFacade()
            ->cancelAuthorization($orderEntity->getIdSalesOrder());

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
