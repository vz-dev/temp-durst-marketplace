<?php
/**
 * Durst - project - IsTourExported.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.10.21
 * Time: 16:40
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition;

use DateTime;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;

/**
 * Class IsTourExported
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class IsTourExported extends AbstractPlugin implements ConditionInterface
{
    public const NAME = 'CancelOrder/IsTourExported';

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function check(
        SpySalesOrderItem $orderItem
    ): bool
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $orderItem
                    ->getFkSalesOrder()
            );

        $tour = $this
            ->getFactory()
            ->getTourFacade()
            ->getConcreteTourById(
                $orderTransfer
                    ->getFkTour()
            );

        // check, if tour has already started
        $tourStarted = $this
            ->hasTourStarted(
                $tour
            );
        // check, if goods exported flag is set on tour
        $goodsExported = $this
            ->hasTourGoodExportStatus(
                $tour
            );
        // check, if the edifact logger has a successful entry
        $logEntrySuccess = $this
            ->areGoodsExportedSuccessfully(
                $tour
            );

        return (
            $tourStarted ||
            $goodsExported ||
            $logEntrySuccess
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $tourTransfer
     * @return bool
     * @throws \Exception
     */
    protected function hasTourStarted(
        ConcreteTourTransfer $tourTransfer
    ): bool
    {
        $prepStart = $tourTransfer
            ->getPreparationStart();

        if (is_string($prepStart)) {
            $prepStart = new DateTime($prepStart);
        }

        $now = new DateTime('now');

        return ($prepStart < $now);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $tourTransfer
     * @return bool
     */
    protected function hasTourGoodExportStatus(
        ConcreteTourTransfer $tourTransfer
    ): bool
    {
        return ($tourTransfer->getGoodsEdiStatus() === DstConcreteTourTableMap::COL_GOODS_EDI_STATUS_SUCCESS);
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $tourTransfer
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function areGoodsExportedSuccessfully(
        ConcreteTourTransfer $tourTransfer
    ): bool
    {
        return $this
            ->getFactory()
            ->getEdifactFacade()
            ->areGoodsExportedSuccessfully(
                $tourTransfer
                    ->getIdConcreteTour()
            );
    }
}
