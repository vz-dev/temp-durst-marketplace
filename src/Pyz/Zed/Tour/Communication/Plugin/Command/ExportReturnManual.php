<?php
/**
 * Durst - project - ExportReturnManual.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-09
 * Time: 16:55
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Command;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;

/**
 * Class ExportReturnManual
 * @package Pyz\Zed\Tour\Communication\Plugin\Command
 * @method TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class ExportReturnManual extends AbstractExportReturn implements CommandPluginInterface
{
    public const COMMAND_NAME = 'WholesaleTour/ExportReturnManual';

    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     * @api
     *
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer): void
    {
        $concreteTourTransfer = $this
            ->getFacade()
            ->getConcreteTourById(
                $stateMachineItemTransfer->getIdentifier()
            );

        $branch = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById(
                $concreteTourTransfer->getFkBranch()
            );

        $statusCode = $this
            ->getFacade()
            ->ediExportDepositById(
                $stateMachineItemTransfer
                    ->getIdentifier(),
                $this->getEdiDepositEndpointUrl($branch),
                TourConstants::EDI_EXPORT_PROCESS_TIMEOUT
            );

        $status = DstConcreteTourTableMap::COL_DEPOSIT_EDI_STATUS_FAILED;

        if ($statusCode === 0) {
            $status = DstConcreteTourTableMap::COL_DEPOSIT_EDI_STATUS_SUCCESS;
        }

        $this
            ->getFacade()
            ->updateConcreteTourDepositEdiStatus(
                $stateMachineItemTransfer->getIdentifier(),
                $status
            );
    }
}
