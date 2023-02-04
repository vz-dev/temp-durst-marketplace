<?php
/**
 * Durst - project - PostDeliveryAreaSaveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 14.11.18
 * Time: 14:07
 */

namespace Pyz\Zed\Touch\Communication\Plugin\DeliveryArea;

use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PostDeliveryAreaSaveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\DeliveryArea
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PostDeliveryAreaSaveTouchPlugin extends AbstractPlugin implements PostDeliveryAreaSavePluginInterface
{
    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea $deliveryArea
     *
     * @return void
     */
    public function save(SpyDeliveryArea $deliveryArea)
    {
        $this->getFacade()->touchActive(DeliveryAreaConstants::RESOURCE_TYPE_DELIVERY_AREA, $deliveryArea->getIdDeliveryArea());
    }
}
