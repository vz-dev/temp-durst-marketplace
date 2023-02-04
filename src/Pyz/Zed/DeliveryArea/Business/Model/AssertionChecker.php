<?php
/**
 * Durst - project - AssertionChecker.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 12:44
 */

namespace Pyz\Zed\DeliveryArea\Business\Model;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;

class AssertionChecker implements ConcreteTimeSlotAssertionInterface
{
    /**
     * @var ConcreteTimeSlotAssertionInterface[]
     */
    protected $assertionStack;

    /**
     * AssertionChecker constructor.
     * @param ConcreteTimeSlotAssertionInterface[] $assertionStack
     */
    public function __construct(array $assertionStack)
    {
        $this->assertionStack = $assertionStack;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity): bool
    {
        foreach($this->assertionStack as $assertion) {
            if($assertion->isValid($concreteTimeSlotEntity) !== true){
                return false;
            }
        }

        return true;
    }
}