<?php
/**
 * Durst - project - TourOrderSorterInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 10:49
 */

namespace Pyz\Zed\Graphhopper\Business\Model;


use Generated\Shared\Transfer\GraphhopperTourTransfer;

interface TourOrderSorterInterface
{
    /**
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     */
    public function orderTourOrders(GraphhopperTourTransfer $graphhopperTourTransfer): GraphhopperTourTransfer;
}
