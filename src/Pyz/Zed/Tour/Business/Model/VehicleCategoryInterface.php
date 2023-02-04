<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 10.09.18
 * Time: 16:44
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\VehicleCategoryTransfer;
use Orm\Zed\Tour\Persistence\DstVehicleCategory;

interface VehicleCategoryInterface
{
    /**
     * @param DstVehicleCategory $vehicleCategoryEntity
     * @return VehicleCategoryTransfer
     */
    public function entityToTransfer(DstVehicleCategory $vehicleCategoryEntity) : VehicleCategoryTransfer;

    /**
     * @return VehicleCategoryTransfer[]
     */
    public function getActiveVehicleCategories() : array;
}
