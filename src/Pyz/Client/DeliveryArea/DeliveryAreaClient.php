<?php
/**
 * Durst - project - DeliveryAreaClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 11:31
 */

namespace Pyz\Client\DeliveryArea;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DeliveryAreaRequestTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class DeliveryAreaClient
 * @package Pyz\Client\DeliveryArea
 * @method DeliveryAreaFactory getFactory()
 */
class DeliveryAreaClient extends AbstractClient implements DeliveryAreaClientInterface
{
    /**
     * @param int $idConcreteTimeslot
     * @return ConcreteTimeSlotTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getConcreteTimeSlotById(int $idConcreteTimeslot): ConcreteTimeSlotTransfer
    {
        $transfer = (new DeliveryAreaRequestTransfer())
            ->setIdConcreteTimeSlot($idConcreteTimeslot);

        return $this
            ->getFactory()
            ->createDeliveryAreaStub()
            ->getBranchesByZipCode($transfer);

    }

    /**
     * {@inheritdoc}
     *
     * @param string $zip
     * @return DeliveryAreaTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCityNameByZipCode(string $zip) : DeliveryAreaTransfer
    {
        $transfer = (new DeliveryAreaRequestTransfer())
            ->setZipCode($zip);

        return $this
            ->getFactory()
            ->createDeliveryAreaStub()
            ->getCityNameByZipCode($transfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zip
     * @param string $branch
     * @return \Generated\Shared\Transfer\DeliveryAreaTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCityNameByZipOrBranchCode(
        string $zip,
        string $branch
    ): DeliveryAreaTransfer
    {

        $transfer = (new DeliveryAreaRequestTransfer())
            ->setZipCode($zip)
            ->setBranchCode($branch);

        return $this
            ->getFactory()
            ->createDeliveryAreaStub()
            ->getCityNameByZipOrBranchCode($transfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranchDeliversZipCode(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaStub()
            ->getBranchDeliversZipCode($requestTransfer);
    }
}
