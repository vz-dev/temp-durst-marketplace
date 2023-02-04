<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:52
 */

namespace Pyz\Zed\DeliveryArea\Business\Model;


use Exception;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryArea;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\Business\Exception\BranchNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\InvalidZipException;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaDeletePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaSavePluginInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class DeliveryArea
{
    const ZIP_MIN = 10000;
    const ZIP_MAX = 99999;

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var PostDeliveryAreaSavePluginInterface[]
     */
    protected $postSavePlugins;

    /**
     * @var PostDeliveryAreaDeletePluginInterface[]
     */
    protected $postDeletePlugins;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphMastersFacade;

    /**
     * DeliveryArea constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     * @param array $postSavePlugins
     * @param array $postDeletePlugins
     * @param MerchantFacadeInterface $merchantFacade
     * @param GraphMastersFacadeInterface $graphMastersFacade
     */
    public function __construct(
        DeliveryAreaQueryContainerInterface $queryContainer,
        array $postSavePlugins,
        array $postDeletePlugins,
        MerchantFacadeInterface $merchantFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->postSavePlugins = $postSavePlugins;
        $this->postDeletePlugins = $postDeletePlugins;
        $this->merchantFacade = $merchantFacade;
        $this->graphMastersFacade = $graphMastersFacade;
    }

    /**
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     * @return DeliveryAreaTransfer
     * @throws DeliveryAreaNotFoundException if the id set in the delivery area transfer
     * cannot be found in the database
     * @throws PropelException
     */
    public function save(DeliveryAreaTransfer $deliveryAreaTransfer)
    {
        //TODO make two different methods one for adding, one for updating
        if($deliveryAreaTransfer->getIdDeliveryArea() === null){
            $deliveryAreaEntity = new SpyDeliveryArea();
        }else{
            $deliveryAreaEntity = $this->getEntityDeliveryAreaById($deliveryAreaTransfer->getIdDeliveryArea());
        }

        $deliveryAreaEntity->setName($deliveryAreaTransfer->getName());
        if($deliveryAreaTransfer->getCity() !== null) {
            $deliveryAreaEntity->setCity($deliveryAreaTransfer->getCity());
        }
        if($deliveryAreaTransfer->getZip() !== null) {
            $deliveryAreaEntity->setZipCode($deliveryAreaTransfer->getZip());
        }

        $deliveryAreaEntity->save();

        $this->runDeliveryAreaPostSavePlugins($deliveryAreaEntity);

        $deliveryAreaTransfer = $this->entityToTransfer($deliveryAreaEntity);

        return $deliveryAreaTransfer;
    }

    /**
     * @param SpyDeliveryArea $deliveryArea
     * @return void
     */
    protected function runDeliveryAreaPostSavePlugins(SpyDeliveryArea $deliveryArea){
        foreach($this->postSavePlugins as $savePlugin)
        {
            $savePlugin->save($deliveryArea);
        }
    }

    /**
     * @param int $idDeliveryArea
     * @return SpyDeliveryArea
     * @throws DeliveryAreaNotFoundException if there is no delivery area with the given id
     */
    protected function getEntityDeliveryAreaById($idDeliveryArea)
    {
        $entity = $this
            ->queryContainer
            ->queryDeliveryAreaById($idDeliveryArea)
            ->findOne();

        if($entity === null){
            throw new DeliveryAreaNotFoundException();
        }

        return $entity;
    }

    /**
     * @param SpyDeliveryArea $entity
     * @return DeliveryAreaTransfer
     */
    public function entityToTransfer(SpyDeliveryArea $entity)
    {
        $transfer = new DeliveryAreaTransfer();
        $transfer->fromArray($entity->toArray(), true);
        $transfer->setZip($entity->getZipCode());

        return $transfer;
    }

    /**
     * @param $name
     * @param $city
     * @param $zip
     * @return DeliveryAreaTransfer
     * @throws DeliveryAreaNotFoundException
     * @throws InvalidZipException if the zip code doesn'z match the range from
     * zip_min to zip_max
     * @throws PropelException
     */
    public function addDeliveryArea($name, $city, $zip)
    {
        //TODO replace the parameters by one merchant transfer object that will be persisted
        if($zip < self::ZIP_MIN || $zip > self::ZIP_MAX){
            throw new InvalidZipException();
        }

        $transfer = new DeliveryAreaTransfer();
        $transfer->setName($name);
        $transfer->setCity($city);
        $transfer->setZip($zip);

        return $this->save($transfer);
    }

    /**
     * @param int $idDeliveryArea
     * @throws DeliveryAreaNotFoundException if the given id cannot be
     * found in the database
     * @throws PropelException
     */
    public function removeDeliveryArea($idDeliveryArea)
    {
        $deliveryAreaEntity = $this
            ->queryContainer
            ->queryDeliveryAreaById($idDeliveryArea)
            ->findOne();

        if($deliveryAreaEntity === null){
            throw new DeliveryAreaNotFoundException();
        }

        $deliveryAreaEntity->delete();
        $this->runDeliveryAreaPostDeletePlugins($deliveryAreaEntity);
    }

    /**
     * @param SpyDeliveryArea $deliveryArea
     * @return void
     */
    protected function runDeliveryAreaPostDeletePlugins(SpyDeliveryArea $deliveryArea){
        foreach($this->postDeletePlugins as $savePlugin)
        {
            $savePlugin->delete($deliveryArea);
        }
    }

    /**
     * @param int $idDeliveryArea
     * @return bool
     */
    public function hasDeliveryArea($idDeliveryArea)
    {
        return $this
            ->queryContainer
            ->queryDeliveryAreaById($idDeliveryArea)
            ->count() > 0;
    }

    /**
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreas()
    {
        $deliveryAreaEntities = $this
            ->queryContainer
            ->queryDeliveryArea()
            ->find();

        $deliveryAreaTransfers = [];
        foreach ($deliveryAreaEntities as $deliveryAreaEntity){
            $deliveryAreaTransfers[] = $this->entityToTransfer($deliveryAreaEntity);
        }

        return $deliveryAreaTransfers;
    }

    /**
     * @param int $idBranch
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasByIdBranch($idBranch)
    {
        $deliveryAreaEntities = $this
            ->queryContainer
            ->queryDeliveryAreaByIdBranch($idBranch)
            ->find();

        $deliveryAreaTransfers = [];
        foreach($deliveryAreaEntities as $deliveryAreaEntity){
            $deliveryAreaTransfers[] = $this->entityToTransfer($deliveryAreaEntity);
        }

        return $deliveryAreaTransfers;
    }

    /**
     * @param int $idDeliveryArea
     * @return DeliveryAreaTransfer
     * @throws DeliveryAreaNotFoundException
     */
    public function getDeliveryAreaById($idDeliveryArea)
    {
        $deliveryAreaEntity = $this
            ->queryContainer
            ->queryDeliveryAreaById($idDeliveryArea)
            ->findOne();

        if($deliveryAreaEntity === null){
            throw new DeliveryAreaNotFoundException(
                sprintf(
                    DeliveryAreaNotFoundException::NOT_FOUND,
                    $idDeliveryArea
                )
            );
        }

        return $this->entityToTransfer($deliveryAreaEntity);
    }

    /**
     * @param int $zip
     * @param string $name
     * @return DeliveryAreaTransfer
     * @throws DeliveryAreaNotFoundException if no delivery area with the given
     * zip code and name exists in the database
     */
    public function getDeliveryAreaByZipAndName($zip, $name)
    {
        $deliveryAreaEntity = $this
            ->queryContainer
            ->queryDeliveryAreaByZipAndName($zip, $name)
            ->findOne();

        if($deliveryAreaEntity === null){
            throw new DeliveryAreaNotFoundException(
                sprintf(
                    DeliveryAreaNotFoundException::NOT_FOUND_ZIP_NAME,
                    $zip,
                    $name
                )
            );
        }

        return $this->entityToTransfer($deliveryAreaEntity);
    }

    /**
     * @return DeliveryAreaTransfer[]
     */
    public function getDeliveryAreasWithoutTimeSlots()
    {
        $deliveryAreaEntities = $this
            ->queryContainer
            ->queryDeliveryArea()
            ->find();

        $transfers = [];
        foreach($deliveryAreaEntities as $entity){
            $transfer = new DeliveryAreaTransfer();
            $transfer->setName($entity->getName());
            $transfer->setZip($entity->getZipCode());
            $transfer->setCity($entity->getCity());
            $transfer->setIdDeliveryArea($entity->getIdDeliveryArea());

            $transfers[] = $transfer;
        }

        return $transfers;
    }

    /**
     * @param string $zipCode
     * @return DeliveryAreaTransfer
     * @throws DeliveryAreaNotFoundException
     */
    public function getDeliveryAreaByZipCode(string $zipCode)
    {
        $entity = $this
            ->queryContainer
            ->queryDeliveryArea()
            ->findOneByZipCode($zipCode);

        if($entity === null){
            throw new DeliveryAreaNotFoundException(
                sprintf(
                    DeliveryAreaNotFoundException::NOT_FOUND_ZIP,
                    $zipCode
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param string $zipCode
     * @param string $branchCode
     * @return DeliveryAreaTransfer
     * @throws BranchNotFoundException
     * @throws DeliveryAreaNotFoundException
     */
    public function getDeliveryAreaByZipOrBranchCode(
        string $zipCode,
        string $branchCode
    ): DeliveryAreaTransfer
    {
        if (empty($zipCode) === true) {
            $branch = $this
                ->getBranchByBranchCode(
                    $branchCode
                );

            if ($branch !== null) {
                $zipCode = $branch
                    ->getZip();}

        }

        return $this
            ->getDeliveryAreaByZipCode(
                $zipCode
            );
    }

    /**
     * @param string $branchCode
     * @return BranchTransfer|null
     * @throws BranchNotFoundException
     */
    protected function getBranchByBranchCode(string $branchCode): ?BranchTransfer
    {
        try {
            return $this
                ->merchantFacade
                ->getBranchByCode(
                    $branchCode
                );
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliveryAreaByZipAndBranchCode(
        string $zipCode,
        string $branchCode
    ): bool
    {
        $deliveryArea = $this
            ->queryContainer
            ->queryDeliveryAreaByZipAndBranchCode(
                $zipCode,
                $branchCode
            )
            ->findOne();

        if ($deliveryArea instanceof SpyDeliveryArea) {
            return true;
        }

        $gmDelivers = $this->graphMastersFacade->getDeliversByZipAndBranchCode($zipCode, $branchCode);

        if ($gmDelivers === true) {
            return true;
        }


        return false;
    }
}
