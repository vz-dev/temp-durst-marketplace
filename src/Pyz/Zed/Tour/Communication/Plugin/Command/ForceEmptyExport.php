<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Command;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;

/**
 * @method TourFacadeInterface getFacade()
 * @method TourCommunicationFactory getFactory()
 */
class ForceEmptyExport extends AbstractCommand implements CommandPluginInterface
{
    public const COMMAND_NAME = 'WholesaleTour/ForceEmptyExport';

    /**
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     *
     * @throws ConcreteTourNotExistsException
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer): void
    {
        $idConcreteTour = $stateMachineItemTransfer->getIdentifier();

        $this
            ->getFacade()
            ->flagConcreteTourForForcedEmptyExport($idConcreteTour);

        $this
            ->getFacade()
            ->flagConcreteTourForExport($idConcreteTour);
    }
}
