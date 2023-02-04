<?php
/**
 * Durst - project - DeliveryAreaStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 11:31
 */

namespace Pyz\Client\DeliveryArea\Zed;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaRequestTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

class DeliveryAreaStub implements DeliveryAreaStubInterface
{
    const URL_GET_CONCRETE_TIME_SLOT_BY_ID = '/delivery-area/gateway/get-concrete-time-slot-by-id';
    const URL_GET_CITY_NAME_BY_ZIP_CODE = '/delivery-area/gateway/get-city-name-by-zip-code';
    const URL_GET_CITY_NAME_BY_ZIP_OR_BRANCH_CODE = '/delivery-area/gateway/get-city-name-by-zip-or-branch-code';
    const URL_BRANCH_DELIVERS_ZIP_CODE = '/delivery-area/gateway/get-branch-delivers-zip-code';

    /**
     * @var ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * DeliveryAreaStub constructor.
     * @param ZedRequestClientInterface $zedStub
     */
    public function __construct(ZedRequestClientInterface $zedStub)
    {

        $this->zedStub = $zedStub;
    }

    /**
     * @param DeliveryAreaRequestTransfer $transfer
     * @return ConcreteTimeSlotTransfer|TransferInterface
     */
    public function getBranchesByZipCode(DeliveryAreaRequestTransfer $transfer) : ConcreteTimeSlotTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_CONCRETE_TIME_SLOT_BY_ID,
            $transfer,
            null
        );
    }

    /**
     * @param DeliveryAreaRequestTransfer $transfer
     * @return DeliveryAreaTransfer|TransferInterface
     */
    public function getCityNameByZipCode(DeliveryAreaRequestTransfer $transfer) : DeliveryAreaTransfer
    {
        return $this->zedStub->call(
            self::URL_GET_CITY_NAME_BY_ZIP_CODE,
            $transfer,
            null
        );
    }

    /**
     * @param \Generated\Shared\Transfer\DeliveryAreaRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\DeliveryAreaTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getCityNameByZipOrBranchCode(DeliveryAreaRequestTransfer $transfer)
    {
        return $this->zedStub->call(
            self::URL_GET_CITY_NAME_BY_ZIP_OR_BRANCH_CODE,
            $transfer,
            null
        );
    }
    /**
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getBranchDeliversZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_BRANCH_DELIVERS_ZIP_CODE,
                $requestTransfer,
                null
            );
    }
}
