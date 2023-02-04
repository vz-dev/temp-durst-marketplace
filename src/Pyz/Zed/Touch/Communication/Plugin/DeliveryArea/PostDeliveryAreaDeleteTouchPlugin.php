<?php
/**
 * Durst - project - PostDeliveryAreaDeleteTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 14.11.18
 * Time: 14:12
 */

namespace Pyz\Zed\Touch\Communication\Plugin\DeliveryArea;

use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaDeletePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostDeliveryAreaDeleteTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\DeliveryArea
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PostDeliveryAreaDeleteTouchPlugin extends AbstractPlugin implements PostDeliveryAreaDeletePluginInterface
{
    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea $deliveryArea
     *
     * @return void
     */
    public function delete(SpyDeliveryArea $deliveryArea)
    {
        $this->getFacade()->touchDeleted(DeliveryAreaConstants::RESOURCE_TYPE_DELIVERY_AREA, $deliveryArea->getIdDeliveryArea());
    }
}
