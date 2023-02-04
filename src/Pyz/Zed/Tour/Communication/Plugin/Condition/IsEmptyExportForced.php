<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Condition;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface;

/**
 * @method TourFacadeInterface getFacade()
 * @method TourCommunicationFactory getFactory()
 */
class IsEmptyExportForced extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/IsEmptyExportForced';

    /**
     * This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        $idConcreteTour = $stateMachineItemTransfer->getIdentifier();

        return $this->isEmptyExportForced($idConcreteTour);
    }

    /**
     * @param int $idConcreteTour
     *
     * @return bool
     */
    protected function isEmptyExportForced(int $idConcreteTour): bool
    {
        $concreteTourTransfer = $this
            ->getFacade()
            ->getConcreteTourById($idConcreteTour);

        $forceEmptyExport = $concreteTourTransfer->getForceEmptyExport();

        return ($forceEmptyExport !== null && $forceEmptyExport === true);
    }
}
