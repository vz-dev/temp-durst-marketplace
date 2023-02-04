<?php
/**
 * Durst - project - TourOrderHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.10.19
 * Time: 11:42
 */

namespace Pyz\Zed\Tour\Business\Sales;


use Generated\Shared\Transfer\OrderTransfer;

class TourOrderHydrator implements TourOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * TourOrderHydrator constructor.
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     */
    public function __construct(\Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateOrderByTourId(OrderTransfer $orderTransfer): OrderTransfer
    {
        $tourEntity = $this
            ->queryContainer
            ->queryConcreteTourByIdOrder($orderTransfer->getIdSalesOrder())
            ->findOne();

        $orderTransfer->setFkTour(null);

        if($tourEntity !== null){
            $orderTransfer->setFkTour($tourEntity->getIdConcreteTour());
            $orderTransfer->setIdTourItemState($tourEntity->getFkStateMachineItemState());
            $orderTransfer->setTourReference($tourEntity->getTourReference());
        }

        return $orderTransfer;
    }
}
