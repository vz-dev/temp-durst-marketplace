<?php
/**
 * Durst - project - TimeSlotHandlerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 22:22
 */

namespace Pyz\Zed\GraphMasters\Business\Handler;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;

interface TimeSlotHandlerInterface
{
    /**
     * @param AppApiRequestTransfer $appApiRequestTransfer
     * @return AppApiResponseTransfer
     */
    public function evaluateTimeSlot(AppApiRequestTransfer $appApiRequestTransfer) : AppApiResponseTransfer;
}
