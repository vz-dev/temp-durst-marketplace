<?php
/**
 * Durst - project - MaxProductsAssertion.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 13:29
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;


use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;

class MaxProductsAssertion implements ConcreteTimeSlotAssertionInterface
{
    /**
     * @var DeliveryAreaConfig
     */
    protected $config;

    /**
     * MaxProductsAssertion constructor.
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
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity, QuoteTransfer $quoteTransfer=null): bool
    {
        if ($concreteTimeSlotEntity->getSpyTimeSlot()->getMaxProducts() === null
            || $concreteTimeSlotEntity->getSpyTimeSlot()->getMaxProducts() === 0
        ) {
            return true;
        }

        return ($this->getAvailableProducts($concreteTimeSlotEntity, $quoteTransfer) >= 0);
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @param QuoteTransfer|null $quoteTransfer
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getAvailableProducts(SpyConcreteTimeSlot $concreteTimeSlotEntity, QuoteTransfer $quoteTransfer=null) : int
    {
        $products = 0;
        if($concreteTimeSlotEntity->getSpySalesOrders() !== null){
            foreach ($concreteTimeSlotEntity->getSpySalesOrders() as $orderEntitiy) {
                foreach ($orderEntitiy->getItems() as $item) {
                    if(!in_array($item->getState()->getName(), $this->getMaxProductValidationStateBlackList())){
                        $products += $item->getQuantity();
                    }
                }
            }
        }

        if($quoteTransfer !== null){
            foreach ($quoteTransfer->getItems() as $item) {
                $products += $item->getQuantity();
            }
        }

        return $concreteTimeSlotEntity->getSpyTimeSlot()->getMaxProducts() - $products;
    }

    /**
     * @return string[]
     */
    protected function getMaxProductValidationStateBlackList(): array
    {
        return $this
            ->config
            ->getMaxCustomerAndProductValidationStateBlackList();
    }
}
