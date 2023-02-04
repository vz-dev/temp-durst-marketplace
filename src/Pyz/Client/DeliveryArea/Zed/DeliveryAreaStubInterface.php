<?php
/**
 * Durst - project - DeliveryAreaStubInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.21
 * Time: 11:54
 */

namespace Pyz\Client\DeliveryArea\Zed;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaRequestTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;

interface DeliveryAreaStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\DeliveryAreaRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\ConcreteTimeSlotTransfer
     */
    public function getBranchesByZipCode(DeliveryAreaRequestTransfer $transfer) : ConcreteTimeSlotTransfer;

    /**
     * @param \Generated\Shared\Transfer\DeliveryAreaRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\DeliveryAreaTransfer
     */
    public function getCityNameByZipCode(DeliveryAreaRequestTransfer $transfer) : DeliveryAreaTransfer;

    /**
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     */
    public function getBranchDeliversZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;
}
