<?php
/**
 * Durst - project - MaxPayloadAssertion.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 05.12.18
 * Time: 13:22
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;


use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;

class MaxPayloadAssertion
{
    /**
     * @var DepositFacadeInterface
     */
    protected $depositFacade;

    /**
     * @var DeliveryAreaConfig
     */
    protected $config;

    /**
     * MaxPayloadAssertion constructor.
     * @param DepositFacadeInterface $depositFacade
     * @param DeliveryAreaConfig $config
     */
    public function __construct(DepositFacadeInterface $depositFacade, DeliveryAreaConfig $config)
    {
        $this->depositFacade = $depositFacade;
        $this->config = $config;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @param QuoteTransfer|null $quoteTransfer
     * @param CartChangeTransfer|null $cartChangeTransfer
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity, QuoteTransfer $quoteTransfer=null, CartChangeTransfer $cartChangeTransfer=null): bool
    {
        if(!$concreteTimeSlotEntity->getDstConcreteTour()){
            return true;
        }

        return ($this->getRemainingPayload($concreteTimeSlotEntity, $quoteTransfer, $cartChangeTransfer) >= 0);
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @param QuoteTransfer|null $quoteTransfer
     * @param CartChangeTransfer|null $cartChangeTransfer
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getRemainingPayload(SpyConcreteTimeSlot $concreteTimeSlotEntity, QuoteTransfer $quoteTransfer=null, CartChangeTransfer $cartChangeTransfer=null) : int
    {
        $concreteTimeSlots = $concreteTimeSlotEntity->getDstConcreteTour()->getSpyConcreteTimeSlots();

        $weight = 0;
        foreach($concreteTimeSlots as $concreteTimeSlot){
            if($concreteTimeSlot->getSpySalesOrders() !== null){
                foreach ($concreteTimeSlot->getSpySalesOrders() as $orderEntity) {
                    if($this->checkIfBlacklistedOrder($orderEntity) === false)
                    {
                        $weight += $orderEntity->getLastOrderTotals()->getWeightTotal();
                    }
                }
            }
        }

        if($quoteTransfer !== null){
            $weight += $quoteTransfer->getTotals()->getWeightTotal();
        }elseif ($cartChangeTransfer !== null){
            $weight += $cartChangeTransfer->getQuote()->getTotals()->getWeightTotal();
        }

        return $this->getTourMaxPayloadInGrams($concreteTimeSlotEntity) - $weight;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return int
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getTourMaxPayloadInGrams(SpyConcreteTimeSlot $concreteTimeSlotEntity) : int
    {
        $maxPayload = $concreteTimeSlotEntity
            ->getDstConcreteTour()
            ->getDstAbstractTour()
            ->getDstVehicleType()
            ->getPayloadKg();

        return $maxPayload * 1000;
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

    /**
     * @param SpySalesOrder $orderEntity
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function checkIfBlacklistedOrder(SpySalesOrder $orderEntity) : bool
    {
        foreach ($orderEntity->getItems() as $item)
        {
            if(in_array($item->getState()->getName(), $this->getMaxProductValidationStateBlackList()) === true)
            {
                return true;
            }
        }

        return false;
    }
}