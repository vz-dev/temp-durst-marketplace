<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 20.08.18
 * Time: 16:25
 */

namespace Pyz\Zed\Tour\Business\Model\VehicleHydrator;


use Generated\Shared\Transfer\VehicleTransfer;
use Orm\Zed\Tour\Persistence\DstVehicle;
use Pyz\Zed\Tour\Business\Model\DrivingLicenceInterface;

class DrivingLicenceHydrator implements VehicleHydratorInterface
{
    /**
     * @var DrivingLicenceInterface
     */
    protected $drivingLicenceModel;

    /**
     * DrivingLicenceHydrator constructor.
     * @param DrivingLicenceInterface $drivingLicenceModel
     */
    public function __construct(DrivingLicenceInterface $drivingLicenceModel)
    {
        $this->drivingLicenceModel = $drivingLicenceModel;
    }

    /**
     * @param DstVehicle $vehicleEntity
     * @param VehicleTransfer $vehicleTransfer
     * @return void
     */
    public function hydrateVehicle(
        DstVehicle $vehicleEntity,
        VehicleTransfer $vehicleTransfer)
    {
        $drivingLicenceTransfer = $this
            ->drivingLicenceModel
            ->getDrivingLicenceById($vehicleEntity->getFkDrivingLicence());
        $vehicleTransfer->setDrivingLicence($drivingLicenceTransfer);
    }

}
