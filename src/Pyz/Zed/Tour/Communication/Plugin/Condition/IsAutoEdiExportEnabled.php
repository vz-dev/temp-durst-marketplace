<?php
/**
 * Durst - project - IsAutoEdiExportEnabled.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-09
 * Time: 16:01
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Condition;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface;

/**
 * Class IsAutoEdiExportEnabled
 * @package Pyz\Zed\Tour\Communication\Plugin\Condition
 * @method TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class IsAutoEdiExportEnabled extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/IsAutoEdiExportEnabled';

    /**
     * This method is called when transition in SM xml file have concrete condition assigned.
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     * @api
     *
     */
    public function check(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        $concreteTourTransfer = $this
            ->getFacade()
            ->getConcreteTourById($stateMachineItemTransfer->getIdentifier());

        $branch = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById(
                $concreteTourTransfer->getFkBranch()
            );

        return ($branch
            ->getAutoEdiExport() === true);
    }
}
