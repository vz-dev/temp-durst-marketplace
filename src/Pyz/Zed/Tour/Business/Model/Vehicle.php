<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 31.07.18
 * Time: 12:25
 */

namespace Pyz\Zed\Tour\Business\Model;

use Generated\Shared\Transfer\VehicleTransfer;
use Orm\Zed\Tour\Persistence\DstVehicle;
use Orm\Zed\Tour\Persistence\Map\DstVehicleTableMap;
use Pyz\Zed\Tour\Business\Exception\VehicleExistsException;
use Pyz\Zed\Tour\Business\Exception\VehicleNotExistsException;
use Pyz\Zed\Tour\Business\Exception\VehicleInvalidArgumentException;
use Pyz\Zed\Tour\Business\Model\VehicleHydrator\VehicleHydratorInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class Vehicle
{
    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var VehicleHydratorInterface[]
     */
    protected $hydrators;

    /**
     * Vehicle constructor.
     * @param TourQueryContainerInterface $queryContainer
     * @param array $hydrators
     */
    public function __construct(
        TourQueryContainerInterface $queryContainer,
        array $hydrators)
    {
        $this->queryContainer = $queryContainer;
        $this->hydrators = $hydrators;
    }

    /**
     * @param $idBranch
     * @return VehicleTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getVehiclesByFkBranch($idBranch) : array
    {
        $vehicleEntities = $this
            ->queryContainer
            ->queryVehicle()
            ->filterByFkBranch($idBranch)
            ->filterByStatus(DstVehicleTableMap::COL_STATUS_ACTIVE)
            ->find();

        $vehicleTransfers = [];
        foreach($vehicleEntities as $vehicleEntity){
            $vehicleTransfers[] = $this->entityToTransfer($vehicleEntity);
        }

        return $vehicleTransfers;
    }

    /**
     * @param int $idVehicle
     * @return VehicleTransfer
     * @throws VehicleNotExistsException
     */
    public function getVehicleById(int $idVehicle) : VehicleTransfer
    {
        $vehicleEntity = $this
            ->queryContainer
            ->queryVehicle()
            ->findOneByIdVehicle($idVehicle);

        if($vehicleEntity === null){
            throw new VehicleNotExistsException(
                sprintf(VehicleNotExistsException::MESSAGE, $idVehicle));
        }

        return $this->entityToTransfer($vehicleEntity);
    }

    /**
     * @param VehicleTransfer $vehicleTransfer
     * @return DstVehicle
     * @throws VehicleNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(VehicleTransfer $vehicleTransfer) : DstVehicle
    {
        if ($vehicleTransfer->getIdVehicle() === null){
            return new DstVehicle();
        }

        return $this
            ->queryContainer
            ->queryVehicleById($vehicleTransfer->getIdVehicle())
            ->findOneOrCreate();
    }

    /**
     * @param VehicleTransfer $vehicleTransfer
     * @return VehicleTransfer
     * @throws VehicleInvalidArgumentException
     * @throws VehicleNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws VehicleExistsException
     */
    public function save(VehicleTransfer $vehicleTransfer) : VehicleTransfer
    {
        if($vehicleTransfer->getNumberPlate() === null){
            throw new VehicleInvalidArgumentException(VehicleInvalidArgumentException::MESSAGE);
        }

        if($vehicleTransfer->getFkBranch() === null){
            throw new VehicleInvalidArgumentException(VehicleInvalidArgumentException::MESSAGE);
        }

        $vehicleEntity = $this->findEntityOrCreate($vehicleTransfer);

        $vehicleEntity->fromArray($vehicleTransfer->toArray());
        $this->checkUnique($vehicleEntity);

        if ($vehicleEntity->isNew() || $vehicleEntity->isModified()){
            $vehicleEntity->save();
        }

        return $this->entityToTransfer($vehicleEntity);
    }

    /**
     * @param $idVehicle
     * @return VehicleTransfer
     * @throws VehicleExistsException
     * @throws VehicleInvalidArgumentException
     * @throws VehicleNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function removeVehicle($idVehicle) : VehicleTransfer
    {
        $vehicleEntity = $this->getVehicleById($idVehicle);
        $vehicleEntity->setStatus(DstVehicleTableMap::COL_STATUS_DELETED);

        return $this->save($vehicleEntity);
    }

    /**
     * @param DstVehicle $entity
     * @return void
     * @throws VehicleExistsException
     */
    protected function checkUnique(DstVehicle $entity)
    {
        if($entity->isNew() && ($entity->getIdVehicle() !== null)){
            throw new VehicleExistsException(
                sprintf(
                    VehicleExistsException::MESSAGE,
                    $entity->getIdVehicle()
                )
            );
        }
    }

    /**
     * @param DstVehicle $vehicleEntity
     * @return VehicleTransfer
     */
    protected function entityToTransfer(DstVehicle $vehicleEntity) : VehicleTransfer
    {
        $vehicleTransfer = new VehicleTransfer();
        $vehicleTransfer->fromArray($vehicleEntity->toArray(), true);

        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrateVehicle($vehicleEntity, $vehicleTransfer);
        }

        return $vehicleTransfer;
    }

}
