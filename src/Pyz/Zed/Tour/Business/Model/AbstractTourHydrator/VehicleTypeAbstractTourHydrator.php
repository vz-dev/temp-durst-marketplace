<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 10.09.18
 * Time: 16:39
 */

namespace Pyz\Zed\Tour\Business\Model\AbstractTourHydrator;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Pyz\Zed\Tour\Business\Model\VehicleCategoryInterface;
use Pyz\Zed\Tour\Business\Model\VehicleTypeInterface;

class VehicleTypeAbstractTourHydrator implements AbstractTourHydratorInterface
{
    /**
     * @var VehicleTypeInterface
     */
    protected $vehicleTypeModel;

    /**
     * @var VehicleCategoryInterface
     */
    protected $vehicleCategoryModel;

    /**
     * VehicleTypeHydrator constructor.
     * @param VehicleTypeInterface $vehicleTypeModel
     * @param VehicleCategoryInterface $vehicleCategoryModel
     */
    public function __construct(VehicleTypeInterface $vehicleTypeModel, VehicleCategoryInterface $vehicleCategoryModel)
    {
        $this->vehicleTypeModel = $vehicleTypeModel;
        $this->vehicleCategoryModel = $vehicleCategoryModel;
    }

    /**
     * {@inheritdoc}
     *
     * @param DstAbstractTour $abstractTourEntity
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return void
     */
    public function hydrateAbstractTour(
        DstAbstractTour $abstractTourEntity,
        AbstractTourTransfer $abstractTourTransfer)
    {
        if($abstractTourEntity->getFkVehicleType() !== null)
        {
            $vehicleTypeEntity = $abstractTourEntity->getDstVehicleType();

            $vehicleTypeTransfer = $this
                ->vehicleTypeModel
                ->entityToTransfer($vehicleTypeEntity);

            if ($vehicleTypeEntity->getFkVehicleCategory() !== null) {
                $vehicleCategoryEntity = $vehicleTypeEntity->getDstVehicleCategory();

                $vehicleCategoryTransfer = $this
                    ->vehicleCategoryModel
                    ->entityToTransfer($vehicleCategoryEntity);

                $vehicleTypeTransfer->setVehicleCategory($vehicleCategoryTransfer);
            }

            $abstractTourTransfer->setVehicleType($vehicleTypeTransfer);
        }
    }

}

