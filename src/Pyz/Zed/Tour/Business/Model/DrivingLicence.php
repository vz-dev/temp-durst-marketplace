<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 31.07.18
 * Time: 12:26
 */

namespace Pyz\Zed\Tour\Business\Model;

use Generated\Shared\Transfer\DrivingLicenceTransfer;
use Orm\Zed\Tour\Persistence\DstDrivingLicence;
use Pyz\Zed\Tour\Business\Exception\DrivingLicenceExistsException;
use Pyz\Zed\Tour\Business\Exception\DrivingLicenceInvalidArgumentException;
use Pyz\Zed\Tour\Business\Exception\DrivingLicenceNotExistsException;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class DrivingLicence implements DrivingLicenceInterface
{
    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * DrivingLicence constructor.
     * @param TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @return DrivingLicenceTransfer[]
     */
    public function getDrivingLicences() : array
    {
        $drivingLicenceEntities = $this
            ->queryContainer
            ->queryDrivingLicence()
            ->find();

        $drivingLicenceTransfers = [];
        foreach($drivingLicenceEntities as $drivingLicenceEntity){
            $drivingLicenceTransfers[] = $this->entityToTransfer($drivingLicenceEntity);
        }

        return $drivingLicenceTransfers;

    }

    /**
     * @param int $idDrivingLicence
     * @return DrivingLicenceTransfer
     */
    public function getDrivingLicenceById(int $idDrivingLicence) : DrivingLicenceTransfer
    {
        $drivingLicenceEntity = $this
            ->queryContainer
            ->queryDrivingLicence()
            ->findOneByIdDrivingLicence($idDrivingLicence);

        return $this->entityToTransfer($drivingLicenceEntity);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function drivingLicenceWithCodeExists(string $code) : bool
    {
         return $this
            ->queryContainer
            ->queryDrivingLicence()
            ->findByCode($code)
            ->count() > 0;
    }

    /**
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DrivingLicenceTransfer
     * @throws DrivingLicenceExistsException
     * @throws DrivingLicenceInvalidArgumentException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(DrivingLicenceTransfer $drivingLicenceTransfer) : DrivingLicenceTransfer
    {
        if($drivingLicenceTransfer->getCode() === null){
            throw new DrivingLicenceInvalidArgumentException(DrivingLicenceInvalidArgumentException::MESSAGE);
        }

        $drivingLicenceEntity = $this->findEntityOrCreate($drivingLicenceTransfer);

        $drivingLicenceEntity->fromArray($drivingLicenceTransfer->toArray());
        $this->checkUnique($drivingLicenceEntity);

        if ($drivingLicenceEntity->isNew() || $drivingLicenceEntity->isModified()){
            $drivingLicenceEntity->save();
        }

        return $this->entityToTransfer($drivingLicenceEntity);
    }

    /**
     * @param int $idDrivingLicence
     * @throws DrivingLicenceNotExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function removeById(int $idDrivingLicence){

        $drivingLicenceEntity = $this
            ->queryContainer
            ->queryDrivingLicenceById($idDrivingLicence)
            ->findOne();

        if ($drivingLicenceEntity === null) {
            throw new DrivingLicenceNotExistsException(
                sprintf(DrivingLicenceNotExistsException::MESSAGE,
                    $idDrivingLicence
                )
            );
        }

        $drivingLicenceEntity->delete();
    }

    /**
     * @param DstDrivingLicence $drivingLicenceEntity
     * @return DrivingLicenceTransfer
     */
    protected function entityToTransfer(DstDrivingLicence $drivingLicenceEntity) : DrivingLicenceTransfer
    {
        $drivingLicenceTransfer = new DrivingLicenceTransfer();
        $drivingLicenceTransfer->fromArray($drivingLicenceEntity->toArray());

        return $drivingLicenceTransfer;
    }

    /**
     * @param DrivingLicenceTransfer $drivingLicenceTransfer
     * @return DstDrivingLicence
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(DrivingLicenceTransfer $drivingLicenceTransfer) : DstDrivingLicence
    {
        if ($drivingLicenceTransfer->getIdDrivingLicence() === null){
            return new DstDrivingLicence();
        }

        return $this
            ->queryContainer
            ->queryDrivingLicenceById($drivingLicenceTransfer->getIdDrivingLicence())
            ->findOneOrCreate();
    }

    /**
     * @param DstDrivingLicence $entity
     * @throws DrivingLicenceExistsException
     */
    protected function checkUnique(DstDrivingLicence $entity)
    {
        if($entity->isNew() && $entity->getIdDrivingLicence()){
            throw new DrivingLicenceExistsException(
                sprintf(
                    DrivingLicenceExistsException::ID_EXISTS_MESSAGE,
                    $entity->getIdDrivingLicence()
                )
            );
        }

        if ($entity->isNew() && $this->drivingLicenceWithCodeExists($entity->getCode())){
            throw new DrivingLicenceExistsException(
                sprintf(
                    DrivingLicenceExistsException::CODE_EXISTS_MESSAGE,
                    $entity->getCode()
                )
            );
        }
    }

}
