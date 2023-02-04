<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 20.10.17
 * Time: 13:25
 */

namespace Pyz\Zed\Deposit\Business\Model;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\DepositTransfer;
use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Pyz\Zed\Deposit\Business\Exception\DepositExistsException;
use Pyz\Zed\Deposit\Business\Exception\DepositInvalidArgumentException;
use Pyz\Zed\Deposit\Business\Exception\DepositMissingException;
use Pyz\Zed\Deposit\Business\Exception\DepositNotFoundException;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;

class Deposit implements DepositInterface
{
    /**
     * @var \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface
     */
    protected $queryContainer;

    public const NAME_NOT_NULL = 'The name of the given deposit transfer object cannot be null';
    public const NAME_EXISTS = 'A deposit with the name %s already exists';
    public const DEPOSIT_NOT_FOUND = 'The deposit with the id %d does not exist';
    public const ID_EXISTS = 'A deposit with the id %d already exists';

    /**
     * Deposit constructor.
     *
     * @param \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface $queryContainer
     */
    public function __construct(DepositQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositExistsException if a deposit with the given name already exists
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositInvalidArgumentException if the given name is null
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function save(DepositTransfer $depositTransfer)
    {
        if ($depositTransfer->getName() === null) {
            throw new DepositInvalidArgumentException(self::NAME_NOT_NULL);
        }

        $depositEntityName = $this
            ->queryContainer
            ->queryDepositByName($depositTransfer->getName())
            ->findOne();

        if ($depositTransfer->getIdDeposit() === null) {
            if ($depositEntityName !== null) {
                throw new DepositExistsException(sprintf(self::NAME_EXISTS, $depositTransfer->getName()));
            }

            $depositEntity = new SpyDeposit();
        } else {
            if ($depositEntityName !== null &&
                $depositEntityName->getIdDeposit() !== $depositTransfer->getIdDeposit()) {
                throw new DepositExistsException(sprintf(self::NAME_EXISTS, $depositTransfer->getName()));
            }

            $depositEntity = $this
                ->queryContainer
                ->queryDepositByIdDeposit($depositTransfer->getIdDeposit())
                ->findOne();
        }

        $depositEntity = $this->hydrateEntityFromTransfer($depositTransfer, $depositEntity);

        $depositEntity->save();

        $depositTransfer = $this->entityToTransfer($depositEntity);

        return $depositTransfer;
    }

    /**
     * @param int $idDeposit
     *
     * @return void
     */
    public function remove($idDeposit)
    {
        $depositEntity = $this
            ->queryContainer
            ->queryDepositByIdDeposit($idDeposit)
            ->findOne();

        if ($depositEntity !== null) {
            $depositEntity->delete();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $depositEntity
     *
     * @return \Orm\Zed\Deposit\Persistence\SpyDeposit
     */
    protected function hydrateEntityFromTransfer(DepositTransfer $depositTransfer, SpyDeposit $depositEntity)
    {
        $depositEntity->setName($depositTransfer->getName());
        $depositEntity->setPresentationName($depositTransfer->getPresentationName());
        $depositEntity->setDeposit($depositTransfer->getDeposit());

        return $depositEntity;
    }

    /**
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $depositEntity
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    protected function entityToTransfer(SpyDeposit $depositEntity)
    {
        $depositTransfer = new DepositTransfer();
        $depositTransfer->fromArray($depositEntity->toArray());

        if ($depositEntity->getWeight() === null) {
            $depositTransfer->setWeight(0);
        }

        return $depositTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDeposits()
    {
        $depositEntities = $this
            ->queryContainer
            ->queryDeposit()
            ->find();

        $depositTransfers = [];
        foreach ($depositEntities as $depositEntity) {
            $depositTransfers[] = $this->entityToTransfer($depositEntity);
        }

        return $depositTransfers;
    }

    /**
     * @param int $idDeposit
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositNotFoundException if there is no deposit with the given id in the database
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositById($idDeposit)
    {
        $depositEntity = $this
            ->queryContainer
            ->queryDepositByIdDeposit($idDeposit)
            ->findOne();

        if ($depositEntity === null) {
            throw new DepositNotFoundException(sprintf(self::DEPOSIT_NOT_FOUND, $idDeposit));
        }

        return $this->entityToTransfer($depositEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositExistsException
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function addDeposit(DepositTransfer $depositTransfer)
    {
        if ($depositTransfer->getIdDeposit() !== null) {
            throw new DepositExistsException(sprintf(self::ID_EXISTS, $depositTransfer->getIdDeposit()));
        }

        return $this->save($depositTransfer);
    }

    /**
     * @param int $idDeposit
     *
     * @return bool
     */
    public function hasDeposit($idDeposit)
    {
        return $this
            ->queryContainer
            ->queryDepositByIdDeposit($idDeposit)
            ->count() > 0;
    }

    /**
     * @return bool
     */
    public function depositsAreImported()
    {
        return $this
                ->queryContainer
                ->queryDeposit()
                ->count() > 0;
    }

    /**
     * @param string $sku
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositMissingException
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositForProductBySku(string $sku): DepositTransfer
    {
        $depositEntity = $this
            ->queryContainer
            ->queryDeposit()
            ->useSpyProductQuery()
            ->filterByIsActive(true)
            ->filterBySku($sku)
            ->endUse()
            ->findOne();

        if ($depositEntity === null) {
            throw new DepositMissingException(
                sprintf(
                    DepositMissingException::MESSAGE,
                    $sku
                )
            );
        }

        return $this
            ->entityToTransfer($depositEntity);
    }

    /**
     * @param AppApiRequestTransfer $apiRequestTransfer
     * @return int
     * @throws DepositMissingException
     */
    public function getWeightForApiRequestItems(AppApiRequestTransfer $apiRequestTransfer): int
    {
        $weight = 0;

        if($apiRequestTransfer->getCartItems() !== null){
            foreach ($apiRequestTransfer->getCartItems() as $item){
                $weight += $this->getWeightForDepositBySku($item->getSku()) * $item->getQuantity();
            }
        }

        return $weight;
    }

    /**
     * @param string $sku
     * @return int
     * @throws DepositMissingException
     */
    public function getWeightForDepositBySku(string $sku): int
    {
        $deposit = $this->getDepositForProductBySku($sku);
        return $deposit->getWeight();
    }
}
