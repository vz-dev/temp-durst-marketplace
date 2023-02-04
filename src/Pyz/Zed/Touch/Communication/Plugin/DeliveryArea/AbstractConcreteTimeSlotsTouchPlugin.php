<?php

namespace Pyz\Zed\Touch\Communication\Plugin\DeliveryArea;

use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Touch\Communication\Exception\ConcreteTimeSlotUnexpectedBranchIdException;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

abstract class AbstractConcreteTimeSlotsTouchPlugin extends AbstractPlugin
{
    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     *
     * @return array
     */
    protected function getItemIds(ObjectCollection $concreteTimeSlots): array
    {
        $itemIds = [];

        foreach ($concreteTimeSlots as $concreteTimeSlot) {
            $itemIds[] = $concreteTimeSlot->getIdConcreteTimeSlot();
        }

        return $itemIds;
    }

    /**
     * @param ObjectCollection|SpyConcreteTimeSlot[] $concreteTimeSlots
     *
     * @return int
     * @throws PropelException
     */
    protected function getFkBranch(ObjectCollection $concreteTimeSlots): int
    {
        $fkBranch = null;

        foreach ($concreteTimeSlots as $concreteTimeSlot) {
            $currentFkBranch = $concreteTimeSlot->getSpyTimeSlot()->getFkBranch();

            if ($fkBranch !== null) {
                if ($fkBranch !== $currentFkBranch) {
                    throw new ConcreteTimeSlotUnexpectedBranchIdException(sprintf(
                        ConcreteTimeSlotUnexpectedBranchIdException::MESSAGE,
                        $concreteTimeSlot->getIdConcreteTimeSlot(),
                        $fkBranch,
                        $currentFkBranch
                    ));
                }

                continue;
            }

            $fkBranch = $concreteTimeSlot->getSpyTimeSlot()->getFkBranch();
        }

        return $fkBranch;
    }
}
