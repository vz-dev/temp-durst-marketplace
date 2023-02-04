<?php
/**
 * Durst - project - GraphhopperToTourBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 10:58
 */

namespace Pyz\Zed\Graphhopper\Dependency;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

class GraphhopperToTourBridge implements GraphhopperToTourBridgeInterface
{
    /**
     * @var \Pyz\Zed\Tour\Business\TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * GraphhopperToTourBridge constructor.
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
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\ConcreteTourTransfer
     */
    public function getConcreteTourById(int $idConcreteTour): ConcreteTourTransfer
    {
        return $this
            ->tourFacade
            ->getConcreteTourById($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array
    {
        return $this
            ->tourFacade
            ->getOrdersByIdConcreteTour($idConcreteTour);
    }
}
