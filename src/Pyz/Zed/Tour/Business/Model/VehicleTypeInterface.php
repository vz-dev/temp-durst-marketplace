<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 10.09.18
 * Time: 16:44
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\VehicleTypeTransfer;
use Orm\Zed\Tour\Persistence\DstVehicleType;

interface VehicleTypeInterface
{
    /**
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function getVehicleTypeById(int $idVehicleType) : VehicleTypeTransfer;

    /**
     * @param int $idBranch
     * @return VehicleTypeTransfer[]
     */
    public function getVehicleTypesByFkBranch(int $idBranch) : array;

    /**
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     */
    public function save(VehicleTypeTransfer $vehicleTypeTransfer) : VehicleTypeTransfer;

    /**
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     */
    public function removeVehicleType(int $idVehicleType) : VehicleTypeTransfer;

    /**
     * @param DstVehicleType $vehicleTypeEntity
     * @return VehicleTypeTransfer
     */
    public function entityToTransfer(DstVehicleType $vehicleTypeEntity) : VehicleTypeTransfer;
}
