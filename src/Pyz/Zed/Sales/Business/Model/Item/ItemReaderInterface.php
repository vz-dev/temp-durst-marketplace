<?php
/**
 * Durst - project - ItemReaderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.03.20
 * Time: 15:51
 */

namespace Pyz\Zed\Sales\Business\Model\Item;


use DateTime;
use DateTimeInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface ItemReaderInterface
{
    /**
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer
     */
    public function getOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): array;

    /**
     * @param DateTimeInterface $startDate
     * @param array $processNames
     * @param array $stateNames
     * @return array|ItemTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getOrderItemsByProcessesAndStates(
        DateTimeInterface $startDate,
        array $processNames = [],
        array $stateNames = []
    ): array;
}
