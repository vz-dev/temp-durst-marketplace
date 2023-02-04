<?php
/**
 * Durst - project - PostDeliveryAreaSavePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 13.11.18
 * Time: 14:49
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin;


use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;

interface PostDeliveryAreaSavePluginInterface
{
    /**
     * @param SpyDeliveryArea $deliveryArea
     * @return void
     */
    public function save(SpyDeliveryArea $deliveryArea);
}