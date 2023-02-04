<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 10:52
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\VehicleTypeTransfer;
use Orm\Zed\Tour\Persistence\DstVehicleType;
use Orm\Zed\Tour\Persistence\Map\DstVehicleTypeTableMap;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\VehicleTypeExistsException;
use Pyz\Zed\Tour\Business\Exception\VehicleTypeInvalidArgumentException;
use Pyz\Zed\Tour\Business\Exception\VehicleTypeNotExistsException;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class VehicleType implements VehicleTypeInterface
{
    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * VehicleType constructor.
     * @param TourQueryContainerInterface $queryContainer
     * @param TouchFacadeInterface $touchFacade
     */
    public function __construct(
        TourQueryContainerInterface $queryContainer,
        TouchFacadeInterface $touchFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->touchFacade = $touchFacade;
    }

    /**
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     * @throws VehicleTypeNotExistsException
     */
    public function getVehicleTypeById(int $idVehicleType) : VehicleTypeTransfer
    {
        $vehicleTypeEntity = $this
            ->queryContainer
            ->queryVehicleType()
            ->findOneByIdVehicleType($idVehicleType);

        if($vehicleTypeEntity === null){
            throw new VehicleTypeNotExistsException(
                sprintf(VehicleTypeNotExistsException::MESSAGE, $idVehicleType));
        }

        return $this->entityToTransfer($vehicleTypeEntity);
    }

    /**
     * @param int $idBranch
     * @return VehicleTypeTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getVehicleTypesByFkBranch(int $idBranch) : array
    {
        $vehicleTypeEntities = $this
            ->queryContainer
            ->queryVehicleType()
            ->filterByFkBranch($idBranch)
            ->filterByStatus(DstVehicleTypeTableMap::COL_STATUS_ACTIVE)
            ->find();

        $vehicleTypeTransfers = [];
        foreach($vehicleTypeEntities as $vehicleTypeEntity){
            $vehicleTypeTransfers[] = $this->entityToTransfer($vehicleTypeEntity);
        }

        return $vehicleTypeTransfers;
    }

    /**
     * @param DstVehicleType $vehicleTypeEntity
     * @return VehicleTypeTransfer
     */
    public function entityToTransfer(DstVehicleType $vehicleTypeEntity) : VehicleTypeTransfer
    {
        $vehicleTypeTransfer = new VehicleTypeTransfer();
        $vehicleTypeTransfer->fromArray($vehicleTypeEntity->toArray(), true);

        return $vehicleTypeTransfer;
    }

    /**
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return VehicleTypeTransfer
     * @throws VehicleTypeExistsException
     * @throws VehicleTypeInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(VehicleTypeTransfer $vehicleTypeTransfer) : VehicleTypeTransfer
    {
        if($vehicleTypeTransfer->getFkBranch() === null){
            throw new VehicleTypeInvalidArgumentException(VehicleTypeInvalidArgumentException::NO_FK_BRANCH_MESSAGE);
        }

        $vehicleTypeEntity = $this->findEntityOrCreate($vehicleTypeTransfer);

        $vehicleTypeEntity->fromArray($vehicleTypeTransfer->toArray());
        $this->checkUnique($vehicleTypeEntity);

        if ($vehicleTypeEntity->isNew() || $vehicleTypeEntity->isModified()){
            $vehicleTypeEntity->save();

            $this->touchConcreteTimeslotsWithVehicleType($vehicleTypeEntity->getIdVehicleType());
        }

        return $this->entityToTransfer($vehicleTypeEntity);
    }

    /**
     * @param int $idVehicleType
     * @return VehicleTypeTransfer
     * @throws VehicleTypeExistsException
     * @throws VehicleTypeInvalidArgumentException
     * @throws VehicleTypeNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function removeVehicleType(int $idVehicleType) : VehicleTypeTransfer
    {
        $vehicleTypeTransfer = $this->getVehicleTypeById($idVehicleType);
        $vehicleTypeTransfer->setStatus(DstVehicleTypeTableMap::COL_STATUS_DELETED);

        return $this->save($vehicleTypeTransfer);
    }

    /**
     * @param VehicleTypeTransfer $vehicleTypeTransfer
     * @return DstVehicleType
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(VehicleTypeTransfer $vehicleTypeTransfer) : DstVehicleType
    {
        if ($vehicleTypeTransfer->getIdVehicleType() === null){
            return new DstVehicleType();
        }

        return $this
            ->queryContainer
            ->queryVehicleTypeById($vehicleTypeTransfer->getIdVehicleType())
            ->findOneOrCreate();
    }

    /**
     * @param DstVehicleType $entity
     * @throws VehicleTypeExistsException
     */
    protected function checkUnique(DstVehicleType $entity)
    {
        if($entity->isNew() && ($entity->getIdVehicleType() !== null)){
            throw new VehicleTypeExistsException(
                sprintf(
                    VehicleTypeExistsException::ID_EXISTS_MESSAGE,
                    $entity->getIdVehicleType()
                )
            );
        }
    }

    /**
     * @param int $idVehicleType
     *
     * @return void
     */
    protected function touchConcreteTimeslotsWithVehicleType(int $idVehicleType)
    {
        $concreteTimeSlots = $this
            ->queryContainer
            ->queryFutureConcreteTimeSlotsWithVehicleType($idVehicleType)
            ->find();

        foreach ($concreteTimeSlots as $concreteTimeSlot)
        {
            $this
                ->touchFacade
                ->touchActive(
                    DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT,
                    $concreteTimeSlot->getIdConcreteTimeSlot()
                );
        }
    }

}
