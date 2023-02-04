<?php
/**
 * Durst - project - ExportGoods.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Communication\Plugin\Command;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Pyz\Shared\Tour\TourConstants;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;

/**
 * Class ExportGoods
 * @package Pyz\Zed\Tour\Communication\Plugin\Command
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class ExportGoods extends AbstractCommand implements CommandPluginInterface
{
    public const COMMAND_NAME = 'WholesaleTour/ExportGoods';

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

        $endpointUrl = $branch
            ->getEdiEndpointUrl();

        if($endpointUrl === null){
            return;
        }

        $statusCode = $this
            ->getFacade()
            ->ediExportTourById(
                $stateMachineItemTransfer
                    ->getIdentifier(),
                $endpointUrl,
                TourConstants::EDI_EXPORT_PROCESS_TIMEOUT
            );



        $concreteTourTransfer->setGoodsEdiStatus(DstConcreteTourTableMap::COL_GOODS_EDI_STATUS_FAILED);

        if ($statusCode === 0) {
            $concreteTourTransfer->setGoodsEdiStatus(DstConcreteTourTableMap::COL_GOODS_EDI_STATUS_SUCCESS);
        }

        $this
            ->getFacade()
            ->updateConcreteTour(
                $concreteTourTransfer
            );
    }
}
