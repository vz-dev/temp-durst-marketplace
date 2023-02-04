<?php


namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\VehicleCategoryTransfer;
use Orm\Zed\Tour\Persistence\DstVehicleCategory;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class VehicleCategory implements VehicleCategoryInterface
{
    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * VehicleType constructor.
     * @param TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return VehicleCategoryTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveVehicleCategories() : array
    {
        $vehicleCategoryEntities = $this
            ->queryContainer
            ->queryVehicleCategoryActive()
            ->find();

        $vehicleCategoryTransfers = [];
        foreach($vehicleCategoryEntities as $vehicleCategoryEntity){
            $vehicleCategoryTransfers[] = $this->entityToTransfer($vehicleCategoryEntity);
        }

        return $vehicleCategoryTransfers;
    }

    /**
     * @param DstVehicleCategory $vehicleCategoryEntity
     * @return VehicleCategoryTransfer
     */
    public function entityToTransfer(DstVehicleCategory $vehicleCategoryEntity) : VehicleCategoryTransfer
    {
        $vehicleCategoryTransfer = new VehicleCategoryTransfer();
        $vehicleCategoryTransfer->fromArray($vehicleCategoryEntity->toArray(), true);

        return $vehicleCategoryTransfer;
    }

}
