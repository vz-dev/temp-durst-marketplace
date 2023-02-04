<?php
/**
 * Durst - project - PaymentMethod.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.21
 * Time: 11:14
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethod;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodExistsException;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class PaymentMethod implements PaymentMethodInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Merchant\Business\Model\MerchantInterface
     */
    protected $merchantModel;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostSavePluginInterface[]
     */
    protected $paymentMethodToBranchPostAddPlugins;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostRemovePluginInterface[]
     */
    protected $paymentMethodToBranchPostRemovePlugins;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostSavePluginInterface[]
     */
    protected $paymentMethodPostSavePlugins;

    /**
     * @var \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostRemovePluginInterface[]
     */
    protected $paymentMethodPostRemovePlugins;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Merchant\Business\Model\MerchantInterface $merchantModel
     * @param array $paymentMethodToBranchPostAddPlugins
     * @param array $paymentMethodToBranchPostRemovePlugins
     * @param array $paymentMethodPostSavePlugins
     * @param array $paymentMethodPostRemovePlugins
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer,
        MerchantInterface               $merchantModel,
        array                           $paymentMethodToBranchPostAddPlugins,
        array                           $paymentMethodToBranchPostRemovePlugins,
        array                           $paymentMethodPostSavePlugins,
        array                           $paymentMethodPostRemovePlugins
    )
    {
        $this->queryContainer = $queryContainer;
        $this->merchantModel = $merchantModel;
        $this->paymentMethodToBranchPostAddPlugins = $paymentMethodToBranchPostAddPlugins;
        $this->paymentMethodToBranchPostRemovePlugins = $paymentMethodToBranchPostRemovePlugins;
        $this->paymentMethodPostSavePlugins = $paymentMethodPostSavePlugins;
        $this->paymentMethodPostRemovePlugins = $paymentMethodPostRemovePlugins;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @param int $idBranch
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removePaymentMethodFromBranch(int $idPaymentMethod, int $idBranch)
    {
        $entity = $this
            ->queryContainer
            ->queryBranchToPaymentMethod()
            ->filterByFkBranch($idBranch)
            ->filterByFkPaymentMethod($idPaymentMethod)
            ->findOne();

        if($entity !== null){
            $entity->delete();
            $this->runPaymentMethodToBranchRemovePlugins($entity);
        }
    }

    /**
     * @param SpyBranchToPaymentMethod $branchToPaymentMethod
     * @return void
     */
    protected function runPaymentMethodToBranchRemovePlugins(
        SpyBranchToPaymentMethod $branchToPaymentMethod
    ): void
    {
        foreach ($this->paymentMethodToBranchPostRemovePlugins as $removePlugin) {
            $removePlugin->remove($branchToPaymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function addPaymentMethodToBranch(int $idPaymentMethod, BranchTransfer $branchTransfer): BranchTransfer
    {
        if($this->hasPaymentMethodByIdPaymentMethodAndIdBranch($idPaymentMethod, $branchTransfer->getIdBranch())){
            return $branchTransfer;
        }

        $entity = new SpyBranchToPaymentMethod();
        $entity->setFkBranch($branchTransfer->getIdBranch());
        $entity->setFkPaymentMethod($idPaymentMethod);
        $entity->save();

        //TODO capsulate the functionality for payment method in this class instead of branch
        $branchTransfer->addPaymentMethods(
            $this
                ->getPaymentMethodById($idPaymentMethod)
        );

        $this->runPaymentMethodToBranchAddPlugins($entity);

        return $branchTransfer;
    }

    /**
     * @param SpyBranchToPaymentMethod $branchToPaymentMethod
     * @return void
     */
    protected function runPaymentMethodToBranchAddPlugins(
        SpyBranchToPaymentMethod $branchToPaymentMethod
    ): void
    {
        foreach ($this->paymentMethodToBranchPostAddPlugins as $addPlugin) {
            $addPlugin->save($branchToPaymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodExistsException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer
    {
        if($paymentMethodTransfer->getIdPaymentMethod() !== null
            && $this->hasPaymentMethodById($paymentMethodTransfer->getIdPaymentMethod())){
            throw new PaymentMethodExistsException(
                sprintf(
                    PaymentMethodExistsException::EXISTS_ID,
                    $paymentMethodTransfer->getIdPaymentMethod()
                )
            );
        }

        $entity = new SpyPaymentMethod();
        $entity->setName($paymentMethodTransfer->getName());
        $entity->setCode($paymentMethodTransfer->getCode());
        $entity->save();

        $this->runPaymentMethodSavePlugins($entity);

        return $this->entityToTransfer($entity);
    }

    /**
     * @param SpyPaymentMethod $paymentMethod
     * @return void
     */
    protected function runPaymentMethodSavePlugins(
        SpyPaymentMethod $paymentMethod
    ): void
    {
        foreach ($this->paymentMethodPostSavePlugins as $savePlugin) {
            $savePlugin->save($paymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer
    {
        if($paymentMethodTransfer->getIdPaymentMethod() === null){
            throw new PaymentMethodNotFoundException(
                PaymentMethodNotFoundException::NO_ID
            );
        }
        if($this->hasPaymentMethodById($paymentMethodTransfer->getIdPaymentMethod()) !== true){
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::NOT_FOUND,
                    $paymentMethodTransfer->getIdPaymentMethod()
                )
            );
        }

        $entity = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->filterByIdPaymentMethod($paymentMethodTransfer->getIdPaymentMethod())
            ->findOne();

        if($entity === null){
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::NOT_FOUND,
                    $paymentMethodTransfer->getIdPaymentMethod()
                )
            );
        }

        $entity->setName($paymentMethodTransfer->getName());
        $entity->setCode($paymentMethodTransfer->getCode());
        $entity->save();

        $this->runPaymentMethodSavePlugins($entity);

        return $this
            ->entityToTransfer($entity);

    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removePaymentMethod(int $idPaymentMethod)
    {
        if($this->hasPaymentMethodById($idPaymentMethod) !== true){
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::NOT_FOUND,
                    $idPaymentMethod
                )
            );
        }

        $entity = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->filterByIdPaymentMethod($idPaymentMethod)
            ->findOne();

        $entity->delete();
    }

    /**
     * @param SpyPaymentMethod $paymentMethod
     * @return void
     */
    protected function runPaymentMethodRemovePlugins(
        SpyPaymentMethod $paymentMethod
    ): void
    {
        foreach ($this->paymentMethodPostRemovePlugins as $removePlugin) {
            $removePlugin->remove($paymentMethod);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getPaymentMethodById(int $idPaymentMethod): PaymentMethodTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->filterByIdPaymentMethod($idPaymentMethod)
            ->findOne();

        if($entity === null){
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::NOT_FOUND,
                    $idPaymentMethod
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return PaymentMethodTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getPaymentMethodsByIdBranch(int $idBranch): array
    {
        $entities = $this
            ->queryContainer
            ->queryBranchToPaymentMethod()
            ->with('SpyPaymentMethod')
            ->filterByFkBranch($idBranch)
            ->find();

        $transfers = [];
        foreach($entities as $entity){
            $transfers[] = $this->entityToTransfer($entity->getSpyPaymentMethod());
        }

        return $transfers;
    }

    /**
     * @param int $idPaymentMethod
     * @param int $idBranch
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function hasPaymentMethodByIdPaymentMethodAndIdBranch(int $idPaymentMethod, int $idBranch): bool
    {
        return ($this
                ->queryContainer
                ->queryBranchToPaymentMethod()
                ->filterByFkBranch($idBranch)
                ->filterByFkPaymentMethod($idPaymentMethod)
                ->count() > 0);
    }

    /**
     * @param int $idPaymentMethod
     * @return bool
     */
    protected function hasPaymentMethodById(int $idPaymentMethod): bool
    {
        return ($this
                ->queryContainer
                ->queryPaymentMethod()
                ->findByIdPaymentMethod($idPaymentMethod)
                ->count() > 0);
    }

    /**
     * @param SpyPaymentMethod $entity
     * @return PaymentMethodTransfer
     */
    protected function entityToTransfer(SpyPaymentMethod $entity): PaymentMethodTransfer
    {
        return (new PaymentMethodTransfer())
            ->fromArray($entity->toArray());
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param string $paymentMethod
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasBranchPaymentMethod(int $idBranch, string $paymentMethod): bool
    {
        return $this
                ->queryContainer
                ->queryBranchToPaymentMethod()
                ->filterByFkBranch($idBranch)
                ->useSpyPaymentMethodQuery()
                ->filterByCode($paymentMethod)
                ->endUse()
                ->count() > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return PaymentMethodTransfer[]
     */
    public function getSupportedPaymentMethodsForBranches(array $branchIds): array
    {
        $entities = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->useSpyBranchToPaymentMethodQuery()
            ->filterByFkBranch_In($branchIds)
            ->endUse()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPaymentMethods(): array
    {
        $entities = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->find();

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return int
     * @throws PaymentMethodNotFoundException
     */
    public function getPaymentMethodIdByCode(string $code): int
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentMethod()
            ->findOneByCode($code);

        if($entity === null){
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::CODE_NOT_FOUND,
                    $code
                )
            );
        }

        return $entity->getIdPaymentMethod();
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsForCurrentBranch(): array
    {
        return $this
            ->getPossiblePaymentMethodsForBranchByMerchantId($this->getCurrentMerchantId());
    }

    /**
     * @param int $idMerchant
     * @return array
     */
    public function getPossiblePaymentMethodsForBranchByMerchantId(int $idMerchant): array
    {
        $entities = $this
            ->queryContainer
            ->queryPossiblePaymentMethodsForMerchant($idMerchant);

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsByIdBranch(int $idBranch): array
    {
        return $this
            ->getPossiblePaymentMethodsByIdBranchByMerchantId($this->getCurrentMerchantId(), $idBranch);
    }


    /**
     * @param int $idMerchant
     * @return array
     */
    public function getPossiblePaymentMethodsByIdBranchByMerchantId(int $idMerchant, int $idBranch): array
    {
        $entities = $this
            ->queryContainer
            ->queryPossiblePaymentMethodsForMerchantByBranchId($idMerchant, $idBranch);

        $transfers = [];
        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @return int
     */
    protected function getCurrentMerchantId(): int
    {
        return $this
            ->merchantModel
            ->getCurrentMerchant()
            ->getIdMerchant();
    }

    /**
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws PaymentMethodNotFoundException
     */
    public function getPaymentMethodByCode(string $code): PaymentMethodTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentMethodByCode($code)
            ->findOne();

        if($entity === null) {
            throw new PaymentMethodNotFoundException(
                sprintf(
                    PaymentMethodNotFoundException::CODE_NOT_FOUND,
                    $code
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }
}
