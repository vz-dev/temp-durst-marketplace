<?php
/**
 * Durst - project - IsReturnExported.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Communication\Plugin\Condition;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface;

/**
 * Class IsReturnExported
 * @package Pyz\Zed\Tour\Communication\Plugin\Condition
 * @method TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class IsReturnExported extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/IsReturnExported';

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
        return $this
            ->getFactory()
            ->getEdifactFacade()
            ->areDepositsExportedSuccessfully($stateMachineItemTransfer->getIdentifier());
    }
}
