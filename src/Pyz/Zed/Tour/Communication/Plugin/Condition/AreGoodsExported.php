<?php
/**
 * Durst - project - AreGoodsExported.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Communication\Plugin\Condition;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\ConditionPluginInterface;

/**
 * Class AreGoodsExported
 * @package Pyz\Zed\Tour\Communication\Plugin\Condition
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class AreGoodsExported extends AbstractPlugin implements ConditionPluginInterface
{
    public const CONDITION_NAME = 'WholesaleTour/AreGoodsExported';

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
            ->areGoodsExportedSuccessfully($stateMachineItemTransfer->getIdentifier());
    }
}
