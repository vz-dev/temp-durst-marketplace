<?php
/**
 * Durst - project - ConcreteTimeSlotTouchPostSaveHook.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-06
 * Time: 19:53
 */

namespace Pyz\Zed\DeliveryArea\Business\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class ConcreteTimeSlotTouchPostSaveHook
{
    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var DeliveryAreaToTouchBridgeInterface
     */
    protected $touchFacade;

    /**
     * ConcreteTimeSlotTouchPostSaveHook constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     * @param DeliveryAreaToTouchBridgeInterface $touchFacade
     */
    public function __construct(DeliveryAreaQueryContainerInterface $queryContainer, DeliveryAreaToTouchBridgeInterface $touchFacade)
    {
        $this->queryContainer = $queryContainer;
        $this->touchFacade = $touchFacade;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     * @return bool
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function touchConcreteTimeSlots(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): bool
    {
        if($quoteTransfer->getUseFlexibleTimeSlots() === true)
        {
            return true;
        }

        $concreteTimeSlotEntity = $this
            ->queryContainer
            ->queryConcreteTimeSlot()
            ->filterByIdConcreteTimeSlot($quoteTransfer->getFkConcreteTimeSlot())
            ->findOne();

        if($concreteTimeSlotEntity->getDstConcreteTour() !== null){
            foreach ($concreteTimeSlotEntity->getDstConcreteTour()->getSpyConcreteTimeSlots() as $concreteTimeSlot)
            {
                $this->touchFacade->touchActive(DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT, $concreteTimeSlot->getIdConcreteTimeSlot());
            }
        }

        return $this->touchFacade->touchActive(DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT, $quoteTransfer->getFkConcreteTimeSlot());
    }
}
