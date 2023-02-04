<?php
/**
 * Durst - project - PostDeliveryAreaDeletePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 13.11.18
 * Time: 14:51
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin;


use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;

interface PostDeliveryAreaDeletePluginInterface
{
    /**
     * @param SpyDeliveryArea $deliveryArea
     * @return void
     */
    public function delete(SpyDeliveryArea $deliveryArea);
}