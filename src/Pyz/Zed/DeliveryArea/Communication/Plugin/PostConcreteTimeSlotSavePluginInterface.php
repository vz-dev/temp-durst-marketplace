<?php
/**
 * Durst - project - PostConcreteTimeSlotSavePluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 14.11.18
 * Time: 09:49
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\Collection\ObjectCollection;

interface PostConcreteTimeSlotSavePluginInterface
{
    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @return void
     */
    public function save(SpyConcreteTimeSlot $concreteTimeSlot);

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     * @return void
     */
    public function bulkSave(ObjectCollection $concreteTimeSlots);
}
