<?php
/**
 * Durst - project - SearchStubInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.18
 * Time: 10:22
 */

namespace Pyz\Client\AppRestApi\Search;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;

interface SearchStubInterface
{
    /**
     * @param array $timeSlotIds
     *
     * @return array
     */
    public function getTimeSlotsForIds(array $timeSlotIds): array;

    /**
     * @param AppApiRequestTransfer $transfer
     * @param bool $fetchFullyBookedTimeSlots
     *
     * @return AppApiResponseTransfer
     */
    public function getTimeSlotsForBranches(
        AppApiRequestTransfer $transfer,
        bool $fetchFullyBookedTimeSlots = false
    ): AppApiResponseTransfer;

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiRequestTransfer
     */
    public function getGMTimeSlots(
        AppApiRequestTransfer $requestTransfer
    ): AppApiRequestTransfer;
}
