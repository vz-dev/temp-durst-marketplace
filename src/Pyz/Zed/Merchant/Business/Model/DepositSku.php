<?php
/**
 * Durst - project - DepositSku.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 14:07
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DepositSkuTransfer;
use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Orm\Zed\Merchant\Persistence\DstBranchToDeposit;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\Merchant\Business\Exception\DepositSkuNotFoundException;
use Pyz\Zed\Merchant\Business\Exception\DepositSkuUnexpectedBranchException;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class DepositSku implements DepositSkuInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface
     */
    protected $depositQueryContainer;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface $depositQueryContainer
     */
    public function __construct(
        MerchantQueryContainerInterface   $queryContainer,
        DepositQueryContainerInterface    $depositQueryContainer
    )
    {
        $this->queryContainer = $queryContainer;
        $this->depositQueryContainer = $depositQueryContainer;
    }


    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getDepositSkusForBranch(BranchTransfer $branchTransfer): array
    {
        $entities = $this
            ->queryContainer
            ->queryBranchToDepositByIdBranch($branchTransfer->getIdBranch())
            ->find();

        $depositIds = [];
        $transfers = [];
        foreach ($entities as $entity) {
            $depositIds[] = $entity->getFkDeposit();
            $transfers[] = $this->entityToTransfer($entity);
        }

        $depositEntities = $this
            ->getDepositsThatDontMatchIdsWithNonZeroDepositValue($depositIds);

        foreach ($depositEntities as $depositEntity) {
            $transfers[] = $this->emptyEntityToTransfer($depositEntity);
        }

        return $transfers;
    }

    /**
     * @param $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getAcceptedDepositSkusForBranch(BranchTransfer $branchTransfer): array
    {
        $entities = $this
            ->queryContainer
            ->queryBranchToDepositWithAcceptedDeposits($branchTransfer->getIdBranch())
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param int $idBranch
     * @param int $idDeposit
     * @return \Generated\Shared\Transfer\DepositSkuTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\DepositSkuNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getDepositSkusByDepositIdForBranch(int $idBranch, int $idDeposit): DepositSkuTransfer
    {
        $depositSkuEntity = $this
            ->queryContainer
            ->queryBranchToDepositWithDeposits(
                $idBranch
            )
            ->filterByFkDeposit($idDeposit)
            ->findOne();

        if($depositSkuEntity === null) {
            throw new DepositSkuNotFoundException(
                sprintf(
                    DepositSkuNotFoundException::DEPOSIT_SKU_NOT_FOUND_BRANCH_DEPOSIT_ID,
                    $idBranch,
                    $idDeposit
                )
            );
        }

        return $this->entityToTransfer($depositSkuEntity);
    }

    /**
     * @return iterable
     */
    protected function getDepositsThatDontMatchIdsWithNonZeroDepositValue(array $depositIds): iterable
    {
        return $this
            ->depositQueryContainer
            ->queryDeposit()
            ->filterByIdDeposit($depositIds, Criteria::NOT_IN)
            ->filterByDeposit(0, Criteria::GREATER_THAN)
            ->find();
    }

    /**
     * @param DepositSkuTransfer[] $depositSkuTransfers
     * @throws \Pyz\Zed\Merchant\Business\Exception\DepositSkuUnexpectedBranchException
     */
    public function updateDepositSkus(iterable $depositSkuTransfers): void
    {
        $fkBranch = 0;
        $idEmptyDepositSkus = [];

        foreach ($depositSkuTransfers as $depositSkuTransfer) {
            if ($fkBranch === 0) {
                $fkBranch = $depositSkuTransfer->getIdBranch();
            }

            if ($fkBranch !== $depositSkuTransfer->getIdBranch()) {
                throw new DepositSkuUnexpectedBranchException(
                    sprintf(
                        DepositSkuUnexpectedBranchException::MESSAGE,
                        $depositSkuTransfer->getIdBranch(),
                        $fkBranch
                    )
                );
            }

            if ($depositSkuTransfer->getSku() !== null
                && $depositSkuTransfer->getSkuBottle() !== null
                && $depositSkuTransfer->getSkuCase() !== null)
            {
                $this->saveDepositSku($depositSkuTransfer);

                continue;
            }

            $idEmptyDepositSkus[] = $depositSkuTransfer->getIdDeposit();
        }

        $this->deleteDepositSkus($fkBranch, $idEmptyDepositSkus);
    }

    /**
     * @param \Generated\Shared\Transfer\DepositSkuTransfer $transfer
     * @return void
     */
    protected function saveDepositSku(DepositSkuTransfer $transfer)
    {
        if($this->needsToBeSaved($transfer) !== true){
            return;
        }

        $entity = $this
            ->queryContainer
            ->queryBranchToDeposit()
            ->filterByFkBranch($transfer->getIdBranch())
            ->filterByFkDeposit($transfer->getIdDeposit())
            ->findOneOrCreate();

        $entity = $this->hydrateEntity($entity, $transfer);

        if($entity->isNew() || $entity->isModified()){
            $entity->save();
        }
    }

    /**
     * @param int $fkBranch
     * @param array $idDepositsToDelete
     */
    protected function deleteDepositSkus(int $fkBranch, array $idDepositsToDelete): void
    {
        $this
            ->queryContainer
            ->queryBranchToDepositByBranchAndDeposits($fkBranch, $idDepositsToDelete)
            ->delete();
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchToDeposit $entity
     * @param \Generated\Shared\Transfer\DepositSkuTransfer $transfer
     * @return \Orm\Zed\Merchant\Persistence\DstBranchToDeposit
     */
    protected function hydrateEntity(DstBranchToDeposit $entity, DepositSkuTransfer $transfer): DstBranchToDeposit
    {
        $entity->setSku($transfer->getSku());
        $entity->setSkuCase($transfer->getSkuCase());
        $entity->setSkuBottle($transfer->getSkuBottle());

        return $entity;
    }

    /**
     * @param \Generated\Shared\Transfer\DepositSkuTransfer $transfer
     * @return bool
     */
    protected function needsToBeSaved(DepositSkuTransfer $transfer): bool
    {
        return (
            $transfer->getSku() !== null &&
            $transfer->getSkuBottle() !== null &&
            $transfer->getSkuCase() !== null
        );
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchToDeposit $entity
     * @return \Generated\Shared\Transfer\DepositSkuTransfer
     */
    protected function entityToTransfer(DstBranchToDeposit $entity): DepositSkuTransfer
    {
        $transfer = new DepositSkuTransfer();
        $transfer->setSku($entity->getSku());
        $transfer->setSkuCase($entity->getSkuCase());
        $transfer->setSkuBottle($entity->getSkuBottle());
        $transfer->setIdBranch($entity->getFkBranch());
        $depositEntity = $entity->getSpyDeposit();
        $transfer->setIdDeposit($depositEntity->getIdDeposit());
        $transfer->setDepositValue($depositEntity->getDeposit());
        $transfer->setDepositCase($depositEntity->getDepositCase());
        $transfer->setDepositBottle($depositEntity->getDepositPerBottle());
        $transfer->setDepositName($depositEntity->getName());

        return $transfer;
    }

    /**
     * @param \Orm\Zed\Deposit\Persistence\SpyDeposit $entity
     * @return \Generated\Shared\Transfer\DepositSkuTransfer
     */
    protected function emptyEntityToTransfer(SpyDeposit $entity): DepositSkuTransfer
    {
        return (new DepositSkuTransfer())
            ->setIdDeposit($entity->getIdDeposit())
            ->setDepositValue($entity->getDeposit())
            ->setDepositCase($entity->getDepositCase())
            ->setDepositBottle($entity->getDepositPerBottle())
            ->setDepositName($entity->getName());
    }
}
