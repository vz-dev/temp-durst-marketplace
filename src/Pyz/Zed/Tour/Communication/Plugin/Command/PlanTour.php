<?php
/**
 * Durst - project - PlanTour.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-09
 * Time: 09:51
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Command;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Graphhopper\Business\Exception\OptimizeException;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;

/**
 * Class PlanTour
 * @package Pyz\Zed\Tour\Communication\Plugin\Command
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 */
class PlanTour extends AbstractCommand implements CommandPluginInterface
{
    public const COMMAND_NAME = 'WholesaleTour/PlanTour';

    /**
     * This method is called when event have concrete command assigned.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $orderTransfer = $this
            ->getFacade()
            ->getOrdersByIdConcreteTour($stateMachineItemTransfer->getIdentifier());

        if(count($orderTransfer) < 1){
            return;
        }

        if ($this->allOrdersHaveLatLng($orderTransfer) === true) {
            $graphhopperTourTransfer = $this
                ->getFacade()
                ->mapTourToGraphhopper($stateMachineItemTransfer->getIdentifier());

            try {
                $graphhopperTourTransfer = $this
                    ->getFactory()
                    ->getGraphhopperFacade()
                    ->orderTourOrders($graphhopperTourTransfer);
            } catch (OptimizeException $e) {
                // TODO notify merchant center and driver app that the tour is not properly optimized
            }

            $this
                ->getFactory()
                ->getSalesFacade()
                ->updateOrdersByGraphhopperOrder($graphhopperTourTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer[] $orderTransfers
     *
     * @return bool
     */
    protected function allOrdersHaveLatLng(array $orderTransfers) : bool
    {
        foreach ($orderTransfers as $orderTransfer) {
            if (empty($orderTransfer->getShippingAddress()->getLat()) || empty($orderTransfer->getShippingAddress()->getLng())) {
                return false;
            }
        }

        return true;
    }
}
