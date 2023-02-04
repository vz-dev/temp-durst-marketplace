<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 09.10.18
 * Time: 13:35
 */

namespace Pyz\Zed\Tour\Business\Model\VehicleHydrator;


use Generated\Shared\Transfer\VehicleTransfer;
use Orm\Zed\Tour\Persistence\DstVehicle;

interface VehicleHydratorInterface
{
    /**
     * @param DstVehicle $vehicleEntity
     * @param VehicleTransfer $vehicleTransfer
     * @return void
     */
    public function hydrateVehicle(
        DstVehicle $vehicleEntity,
        VehicleTransfer $vehicleTransfer);
}
