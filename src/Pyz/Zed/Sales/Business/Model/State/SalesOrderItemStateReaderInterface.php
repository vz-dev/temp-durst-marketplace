<?php
/**
 * Durst - project - SalesOrderItemStateInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.20
 * Time: 09:51
 */

namespace Pyz\Zed\Sales\Business\Model\State;


interface SalesOrderItemStateReaderInterface
{
    /**
     * @param array $stateNames
     * @return int[]
     */
    public function getStateIdsByStateNames(array $stateNames): array;
}
