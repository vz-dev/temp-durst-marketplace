<?php
/**
 * Durst - project - AllOrdersClosed.php.
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
 * Class AllOrdersClosed
 * @package Pyz\Zed\Tour\Communication\Plugin\Condition
 * @method TourFacadeInterface getFacade()
 */
class AllOrdersClosed extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/AllOrdersClosed';

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
        $hasOpenOrders = $this
            ->getFacade()
            ->hasConcreteTourOpenOrdersWithExcludedIds(
                $stateMachineItemTransfer->getIdentifier(),
                [
                    $stateMachineItemTransfer->getTriggeringOrderId(),
                ]
            );

        return ($hasOpenOrders === false);
    }
}
