<?php
/**
 * Durst - project - MaxCustomersAssertion.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 13:26
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;

class MaxCustomersAssertion implements ConcreteTimeSlotAssertionInterface
{
    /**
     * @var DeliveryAreaConfig
     */
    protected $config;

    /**
     * MaxCustomersAssertion constructor.
     * @param DeliveryAreaConfig $config
     */
    public function __construct(DeliveryAreaConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity): bool
    {
        if($concreteTimeSlotEntity->getSpyTimeSlot()->getMaxCustomers() === null) {
            return true;
        }

        return ($this->getAvailableCustomers($concreteTimeSlotEntity) > 0);
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getAvailableCustomers(SpyConcreteTimeSlot $concreteTimeSlotEntity) : int
    {
        $customers = 0;
        if($concreteTimeSlotEntity->getSpySalesOrders() !== null){
            foreach ($concreteTimeSlotEntity->getSpySalesOrders() as $spySalesOrder) {
                foreach ($spySalesOrder->getItems() as $item) {
                    if(!in_array($item->getState()->getName(), $this->getMaxCustomerValidationStateBlackList())) {
                        $customers++;
                        break;
                    }
                }
            }
        }

        return $concreteTimeSlotEntity->getSpyTimeSlot()->getMaxCustomers() - $customers;
    }

    /**
     * @return string[]
     */
    protected function getMaxCustomerValidationStateBlackList(): array
    {
        return $this
            ->config
            ->getMaxCustomerAndProductValidationStateBlackList();
    }
}