<?php
/**
 * Durst - project - CancelOrderConcreteTourHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:19
 */

namespace Pyz\Zed\CancelOrder\Business\Hydrator;

use Generated\Shared\Transfer\CancelOrderTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

/**
 * Class CancelOrderConcreteTourHydrator
 * @package Pyz\Zed\CancelOrder\Business\Hydrator
 */
class CancelOrderConcreteTourHydrator implements CancelOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Tour\Business\TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @param \Pyz\Zed\Tour\Business\TourFacadeInterface $tourFacade
     */
    public function __construct(
        TourFacadeInterface $tourFacade
    )
    {
        $this->tourFacade = $tourFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderTransfer $orderTransfer
     * @return void
     */
    public function hydrateCancelOrder(
        CancelOrderTransfer $orderTransfer
    ): void
    {
        $tour = $this
            ->tourFacade
            ->getConcreteTourById(
                $orderTransfer
                    ->getFkConcreteTour()
            );

        $orderTransfer
            ->setConcreteTour(
                $tour
            );
    }
}
