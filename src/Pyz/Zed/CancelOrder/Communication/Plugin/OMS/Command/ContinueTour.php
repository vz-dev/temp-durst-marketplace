<?php
/**
 * Durst - project - ContinueTour.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.09.21
 * Time: 15:38
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class ContinueTour
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class ContinueTour extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'continueTour';
    public const NAME = 'CancelOrder/ContinueTour';
    public const STATE_NAME = 'continue tour';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        try {
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getOrderByIdSalesOrder(
                    $orderEntity
                        ->getIdSalesOrder()
                );

            $stateMachineItemTransfer = $this
                ->createStateMachineItemTransfer(
                    $orderTransfer->requireIdTourItemState()->getIdTourItemState(),
                    $orderTransfer->requireFkTour()->getFkTour(),
                    $orderTransfer->getIdSalesOrder()
                );
            $this
                ->getFactory()
                ->getStateMachineFacade()
                ->triggerEvent(
                    TourConstants::TOUR_STATE_EVENT_FINISH_DELIVERY,
                    $stateMachineItemTransfer
                );
        } catch (RequiredTransferPropertyException $e) {
            //if no tour id is set we don't need to trigger an event change
        }

        return [];
    }

    /**
     * @param int $idItemState
     * @param int $idTour
     * @param int $idOrder
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer
     */
    protected function createStateMachineItemTransfer(
        int $idItemState,
        int $idTour,
        int $idOrder
    ): StateMachineItemTransfer
    {
        return (new StateMachineItemTransfer())
            ->setIdentifier($idTour)
            ->setIdItemState($idItemState)
            ->setTriggeringOrderId($idOrder);
    }
}
