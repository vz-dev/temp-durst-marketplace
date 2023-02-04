<?php
/**
 * Durst - project - PostConcreteTimeSlotDeletePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 14.11.18
 * Time: 09:48
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\Collection\ObjectCollection;

interface PostConcreteTimeSlotDeletePluginInterface
{
    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @return void
     */
    public function delete(SpyConcreteTimeSlot $concreteTimeSlot);

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     * @return void
     */
    public function bulkDelete(ObjectCollection $concreteTimeSlots);
}
