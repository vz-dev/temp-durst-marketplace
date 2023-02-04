<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 19.06.18
 * Time: 09:28
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class ConcreteTimeSlotOrderHydrationPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Sales
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class ConcreteTimeSlotOrderHydrationPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->hydrateConcreteTimeSlot($orderTransfer);
    }
}