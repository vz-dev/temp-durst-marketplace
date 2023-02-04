<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Condition;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface;

/**
 * @method TourFacadeInterface getFacade()
 * @method TourCommunicationFactory getFactory()
 */
class BranchUsesEdiExportV2 extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/BranchUsesEdiExportV2';

    /**
     * This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     *
     * @throws ContainerKeyNotFoundException
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        $idConcreteTour = $stateMachineItemTransfer->getIdentifier();

        return $this->branchUsesEdiExportV2($idConcreteTour);
    }

    /**
     * @param int $idConcreteTour
     *
     * @return bool
     *
     * @throws ContainerKeyNotFoundException
     */
    protected function branchUsesEdiExportV2(int $idConcreteTour): bool
    {
        $concreteTourTransfer = $this
            ->getFacade()
            ->getConcreteTourById($idConcreteTour);

        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($concreteTourTransfer->getFkBranch());

        $ediExportVersion = $branchTransfer->getEdiExportVersion();

        return ($ediExportVersion !== null && $ediExportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2);
    }
}
