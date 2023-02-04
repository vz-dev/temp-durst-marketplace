<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 09.10.18
 * Time: 11:57
 */

namespace Pyz\Zed\Tour\Business\Model\VehicleHydrator;


use Generated\Shared\Transfer\VehicleTransfer;
use Orm\Zed\Tour\Persistence\DstVehicle;
use Pyz\Zed\Tour\Business\Model\VehicleTypeInterface;


class VehicleTypeHydrator implements VehicleHydratorInterface
{
    /**
     * @var VehicleTypeInterface
     */
    protected $vehicleTypeModel;

    /**
     * VehicleTypeHydrator constructor.
     * @param VehicleTypeInterface $vehicleTypeModel
     */
    public function __construct(VehicleTypeInterface $vehicleTypeModel)
    {
        $this->vehicleTypeModel = $vehicleTypeModel;
    }

    /**
     * {@inheritdoc}
     *
     * @param DstVehicle $vehicleEntity
     * @param VehicleTransfer $vehicleTransfer
     */
    public function hydrateVehicle(
        DstVehicle $vehicleEntity,
        VehicleTransfer $vehicleTransfer)
    {
        $vehicleTypeTransfer = $this
            ->vehicleTypeModel
            ->getVehicleTypeById($vehicleEntity->getFkVehicleType());
        $vehicleTransfer->setVehicleType($vehicleTypeTransfer);
    }

}
