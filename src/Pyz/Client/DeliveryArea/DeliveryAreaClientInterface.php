<?php
/**
 * Durst - project - DeliveryAreaClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 11:30
 */

namespace Pyz\Client\DeliveryArea;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;

interface DeliveryAreaClientInterface
{
    /**
     * Receives the concrete time slot matching the id from zed and returns a fully hydrated transfer object.
     *
     * @param int $idConcreteTimeslot
     * @return ConcreteTimeSlotTransfer
     */
    public function getConcreteTimeSlotById(int $idConcreteTimeslot) : ConcreteTimeSlotTransfer;

    /**
     * Receives a city name matching the zip code (given by the request transfer object)
     * from zed and returns the response transfer.
     * Only one name is returned even when the zip code matches several cities.
     *
     * @param string $zip
     * @return DeliveryAreaTransfer
     */
    public function getCityNameByZipCode(string $zip) : DeliveryAreaTransfer;

    /**
     * Receives a city name matching the zip or branch code (given by the request transfer object)
     * from zed and returns the response transfer.
     * Only one name is returned even when the zip code matches several cities.
     *
     * @param string $zip
     * @param string $branch
     * @return \Generated\Shared\Transfer\DeliveryAreaTransfer
     */
    public function getCityNameByZipOrBranchCode(
        string $zip,
        string $branch
    ) : DeliveryAreaTransfer;

    /**
     * Check if the branch with the given branch_code delivers to the given zip code
     *
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     */
    public function getBranchDeliversZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer;
}
