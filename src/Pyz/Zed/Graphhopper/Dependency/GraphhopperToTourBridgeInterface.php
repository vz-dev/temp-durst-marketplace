<?php
/**
 * Durst - project - GraphhopperToTourBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 10:58
 */

namespace Pyz\Zed\Graphhopper\Dependency;


use Generated\Shared\Transfer\ConcreteTourTransfer;

interface GraphhopperToTourBridgeInterface
{
    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\ConcreteTourTransfer
     */
    public function getConcreteTourById(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\OrderTransfer[]
     */
    public function getOrdersByIdConcreteTour(int $idConcreteTour): array;
}
