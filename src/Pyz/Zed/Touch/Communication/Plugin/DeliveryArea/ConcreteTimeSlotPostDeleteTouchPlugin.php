<?php
/**
 * Durst - project - ConcreteTimeSlotPostDeleteTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 21.11.18
 * Time: 16:03
 */

namespace Pyz\Zed\Touch\Communication\Plugin\DeliveryArea;

use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface;

/**
 * Class ConcreteTimeSlotPostDeleteTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\DeliveryArea
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class ConcreteTimeSlotPostDeleteTouchPlugin extends AbstractConcreteTimeSlotsTouchPlugin implements PostConcreteTimeSlotDeletePluginInterface
{
    /**
     * @param \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot $concreteTimeSlot
     *
     * @return void
     */
    public function delete(SpyConcreteTimeSlot $concreteTimeSlot)
    {
        $this->getFacade()->touchDeleted(DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT, $concreteTimeSlot->getIdConcreteTimeSlot());
        $this->getFacade()->touchActive(MerchantConstants::RESOURCE_TYPE_BRANCH, $concreteTimeSlot->getSpyTimeSlot()->getFkBranch());
    }

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     *
     * @return void
     *
     * @throws PropelException
     */
    public function bulkDelete(ObjectCollection $concreteTimeSlots): void
    {
        $this->getFacade()->bulkTouchSetDeleted(
            DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT,
            $this->getItemIds($concreteTimeSlots)
        );

        $this->getFacade()->touchActive(
            MerchantConstants::RESOURCE_TYPE_BRANCH,
            $this->getFkBranch($concreteTimeSlots)
        );
    }
}
