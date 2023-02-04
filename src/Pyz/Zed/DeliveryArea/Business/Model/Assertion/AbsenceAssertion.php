<?php
/**
 * Durst - project - AbsenceAssertion.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 13:35
 */

namespace Pyz\Zed\DeliveryArea\Business\Model\Assertion;


use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Pyz\Zed\Absence\Business\AbsenceFacadeInterface;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;

class AbsenceAssertion implements ConcreteTimeSlotAssertionInterface
{
    /**
     * @var AbsenceFacadeInterface
     */
    protected $absenceFacade;

    /**
     * AbsenceAssertion constructor.
     * @param AbsenceFacadeInterface $absenceFacade
     */
    public function __construct(AbsenceFacadeInterface $absenceFacade)
    {
        $this->absenceFacade = $absenceFacade;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlotEntity
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function isValid(SpyConcreteTimeSlot $concreteTimeSlotEntity): bool
    {
        return !($this->absenceFacade->isBranchAbsent(
            $concreteTimeSlotEntity->getSpyTimeSlot()->getFkBranch(),
            $concreteTimeSlotEntity->getStartTime() ,
            $concreteTimeSlotEntity->getEndTime()
            )
        );

    }
}