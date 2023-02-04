<?php
/**
 * Durst - project - AccountingToSalesBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:40
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use DateTime;

interface AccountingToSalesBridgeInterface
{
    /**
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer[]
     */
    public function getOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): array;

    /**
     * @param string[] $stateNames
     * @return int[]
     */
    public function getStateIdsByStateNames(array $stateNames): array;
}
