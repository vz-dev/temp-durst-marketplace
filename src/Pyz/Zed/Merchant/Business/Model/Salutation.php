<?php
/**
 * Durst - project - Salutation.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.21
 * Time: 11:28
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\SalutationTransfer;
use Orm\Zed\Merchant\Persistence\SpyEnumSalutation;
use Pyz\Zed\Merchant\Business\Exception\SalutationIdNotSetException;
use Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class Salutation implements SalutationInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function add(SalutationTransfer $salutationTransfer): SalutationTransfer
    {
        $entity = new SpyEnumSalutation();
        $entity->setName($salutationTransfer->getName());
        $entity->save();

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationIdNotSetException if the given transfer object has no id
     * set (can't find salutation without it's id)
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the id set in the transfer
     * object could be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function save(SalutationTransfer $salutationTransfer): SalutationTransfer
    {
        if($salutationTransfer->getIdSalutation() === null){
            throw new SalutationIdNotSetException(
                SalutationIdNotSetException::NO_ID_SET
            );
        }

        $entity = $this
            ->queryContainer
            ->queryEnumSalutation()
            ->filterByIdEnumSalutation($salutationTransfer->getIdSalutation())
            ->findOne();

        if($entity === null){
            throw new SalutationNotFoundException(
                sprintf(
                    SalutationNotFoundException::ID_NOT_FOUND,
                    $salutationTransfer->getIdSalutation()
                )
            );
        }

        $entity->setName($salutationTransfer->getName());
        $entity->save();

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param int $idSalutation
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the given id
     * could be found in the database
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function delete($idSalutation): void
    {
        $entity = $this
            ->queryContainer
            ->queryEnumSalutation()
            ->filterByIdEnumSalutation($idSalutation)
            ->findOne();

        if($entity === null){
            throw new SalutationNotFoundException(
                sprintf(
                    SalutationNotFoundException::ID_NOT_FOUND,
                    $idSalutation
                )
            );
        }

        $entity->delete();
    }

    /**
     * @param int $idSalutation
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException if no salutation with the given id
     * could be found in the database
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getSalutationById(int $idSalutation): SalutationTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryEnumSalutation()
            ->filterByIdEnumSalutation($idSalutation)
            ->findOne();

        if($entity === null){
            throw new SalutationNotFoundException(
                sprintf(
                    SalutationNotFoundException::ID_NOT_FOUND,
                    $idSalutation
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @return bool
     */
    public function enumSalutationsAreImported(): bool
    {
        return $this
                ->queryContainer
                ->queryEnumSalutation()
                ->count() > 0;
    }

    /**
     * @param SpyEnumSalutation $entity
     * @return SalutationTransfer
     */
    protected function entityToTransfer(SpyEnumSalutation $entity): SalutationTransfer
    {
        $transfer = new SalutationTransfer();
        $transfer->setIdSalutation($entity->getIdEnumSalutation());
        $transfer->setName($entity->getName());

        return $transfer;
    }
}
