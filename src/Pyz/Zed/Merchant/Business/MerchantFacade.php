<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:12
 */

namespace Pyz\Zed\Merchant\Business;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\BranchUserTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\DepositSkuTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SalutationTransfer;
use Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException;
use Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * Class MerchantFacade
 * @package Pyz\Zed\Merchant\Business
 * @method MerchantBusinessFactory getFactory()
 */
class MerchantFacade extends AbstractFacade implements MerchantFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $branchData
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildBranchPageMap(PageMapBuilderInterface $pageMapBuilder, array $branchData, LocaleTransfer $localeTransfer): PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createBranchDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $branchData, $localeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $paymentProviderData
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPaymentProviderPageMap(PageMapBuilderInterface $pageMapBuilder, array $paymentProviderData, LocaleTransfer $localeTransfer): PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentProviderDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $paymentProviderData, $localeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function checkBranchSupportsPaymentMethod(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        return $this
            ->getFactory()
            ->createBranchPaymentMethodChecker()
            ->checkBranchSupportsPaymentMethod($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $merchantPin
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getMerchantByMerchantPin($merchantPin);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateOrderByBranch(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createOrderHydrator()
            ->hydrateOrderByBranch($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $branchCode
     * @return \Generated\Shared\Transfer\BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchInactiveException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchByBranchCode(string $branchCode): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchByCode(
                $branchCode
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasCurrentMerchant(): bool
    {
        return $this->getFactory()
            ->createMerchantModel()
            ->hasCurrentMerchant();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasCurrentBranch(): bool
    {
        return $this->getFactory()
            ->createBranchModel()
            ->hasCurrentBranch();
    }

    /**
     * {@inheritdoc}
     *
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCurrentMerchant(): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantModel()
            ->getCurrentMerchant();
    }

    /**
     * {@inheritdoc}
     *
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCurrentBranch(): BranchTransfer
    {
        return $this->getFactory()
            ->createBranchModel()
            ->getCurrentBranch();
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branch
     * @return mixed
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function setCurrentBranch(BranchTransfer $branch)
    {
        return $this->getFactory()
            ->createBranchModel()
            ->setCurrentBranch($branch);
    }

    /**
     * {@inheritdoc}
     *
     * @param MerchantTransfer $merchantTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     */
    public function createMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->save($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchStatusInvalidException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function createBranch(BranchTransfer $branchTransfer)
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->save($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param MerchantTransfer $merchantTransfer
     * @return MerchantTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     */
    public function updateMerchant(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantModel()
            ->save($merchantTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return MerchantTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function removeMerchant(int $idMerchant): MerchantTransfer
    {
        return $this->getFactory()
            ->createMerchantModel()
            ->removeMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function activateMerchant(int $idMerchant): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->activateMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deactivateMerchant(int $idMerchant): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->deactivateMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return array|BranchTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranchesByIdMerchant(int $idMerchant): array
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchesByIdMerchant($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $merchantname
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasMerchantByMerchantname(string $merchantname): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->hasMerchantByMerchantname($merchantname);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchById(int $idBranch): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchById($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $orderedUnits
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @return void
     */
    public function sumUpOrderedUnitsToBranchById(int $idBranch, int $orderedUnits): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->sumUpOrderedUnitsToBranchById($idBranch, $orderedUnits);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $merchantname
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasActiveMerchantByMerchantname(string $merchantname): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->hasActiveMerchantByMerchantname($merchantname);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $merchantname
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantByMerchantname(string $merchantname): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getMerchantByMerchantname($merchantname);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $password
     * @param string $hash
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isValidPassword(string $password, string $hash): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->validatePassword($password, $hash);
    }

    /**
     * {@inheritdoc}
     *
     * @param MerchantTransfer $merchant
     * @return mixed
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function setCurrentMerchant(MerchantTransfer $merchant)
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->setCurrentMerchant($merchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @param bool $hasBranchUser
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getActiveMerchantById(int $idMerchant, bool $hasBranchUser = false): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getActiveMerchantById(
                $idMerchant,
                $hasBranchUser
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idMerchant
     * @return MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getMerchantById($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @return MerchantTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchants(): array
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getMerchants();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isBranchIgnorable(string $bundle, string $controller, string $action): bool
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->isIgnorablePath($bundle, $controller, $action);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function unsetCurrentBranch(): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->logout();
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchStatusInvalidException
     */
    public function updateBranch(BranchTransfer $branchTransfer): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->save($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function addPaymentMethodToBranch(int $idPaymentMethod, BranchTransfer $branchTransfer): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->addPaymentMethodToBranch($idPaymentMethod, $branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function addPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->addPaymentMethod($paymentMethodTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return PaymentMethodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function updatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->updatePaymentMethod($paymentMethodTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @return PaymentMethodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPaymentMethodById(int $idPaymentMethod): PaymentMethodTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPaymentMethodById($idPaymentMethod);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPaymentMethod
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function removePaymentMethod(int $idPaymentMethod): void
    {
        $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->removePaymentMethod($idPaymentMethod);
    }

    /**
     * {@inheritdoc}
     *
     * @param SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addSalutation(SalutationTransfer $salutationTransfer): SalutationTransfer
    {
        return $this
            ->getFactory()
            ->createSalutationModel()
            ->add($salutationTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalutation
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getSalutationById(int $idSalutation): SalutationTransfer
    {
        return $this
            ->getFactory()
            ->createSalutationModel()
            ->getSalutationById($idSalutation);
    }

    /**
     * {@inheritdoc}
     *
     * @param SalutationTransfer $salutationTransfer
     * @return SalutationTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationIdNotSetException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updateSalutation(SalutationTransfer $salutationTransfer): SalutationTransfer
    {
        return $this
            ->getFactory()
            ->createSalutationModel()
            ->save($salutationTransfer);
    }

    /**
     * @param int $idSalutation
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removeSalutation(int $idSalutation): void
    {
        $this
            ->getFactory()
            ->createSalutationModel()
            ->delete($idSalutation);
    }

    /**
     * @param int $idPaymentMethod
     * @param int $idBranch
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function removePaymentMethodFromBranch(int $idPaymentMethod, int $idBranch): void
    {
        $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->removePaymentMethodFromBranch($idPaymentMethod, $idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @return BranchTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranches(): array
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranches();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $zipCode
     * @return BranchTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchesByZipCode(string $zipCode): array
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchesByZipCode($zipCode);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException
     * @throws \Pyz\Zed\Merchant\Business\Exception\SalutationNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchStatusInvalidException
     */
    public function addBranch(BranchTransfer $branchTransfer): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->save($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function removeBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->deleteBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return BranchTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchInactiveException
     */
    public function getBranchByCode(string $code): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param string $paymentMethod
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasBranchPaymentMethod(int $idBranch, string $paymentMethod): bool
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->hasBranchPaymentMethod($idBranch, $paymentMethod);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getSupportedPaymentMethodsForBranches(array $branchIds): array
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getSupportedPaymentMethodsForBranches($branchIds);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPaymentMethods(): array
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPaymentMethods();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function activateCurrentBranch(): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->activateCurrentBranch();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function deactivateCurrentBranch(): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->deactivateCurrentBranch();
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartPreCheckResponseTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchInactiveException
     */
    public function validateBranch(CartChangeTransfer $cartChangeTransfer): CartPreCheckResponseTransfer
    {
        return $this
            ->getFactory()
            ->createBranchValidator()
            ->validateBranchIsActive($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function restoreBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->restoreBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchInactiveException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function validateBranchForCheckout(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) : bool
    {
        return $this
            ->getFactory()
            ->createBranchValidator()
            ->validateBranchIsActiveCheckout($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @param string $zipCode
     * @return BranchTransfer|null
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchByIdAndZipCode(int $id, string $zipCode): ?BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchByIdAndZipCode($id, $zipCode);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return int
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPaymentMethodIdByCode(string $code): int
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPaymentMethodIdByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentMethodTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPossiblePaymentMethodsForCurrentBranch(): array
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPossiblePaymentMethodsForCurrentBranch();
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentMethodTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPossiblePaymentMethodsByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPossiblePaymentMethodsByIdBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPossiblePaymentMethodsForBranchByMerchantId(int $idMerchant): array
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPossiblePaymentMethodsForBranchByMerchantId($idMerchant);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException   if no payment method with the given code
     *                                          can be found.
     */
    public function getPaymentMethodByCode(string $code): PaymentMethodTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentMethodModel()
            ->getPaymentMethodByCode($code);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $hash
     * @return BranchTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException  if no branch can be found that hash the provided
     *                                  hash
     */
    public function getBranchByHash(string $hash) : BranchTransfer
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getBranchByHash($hash);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return string
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getHashForBranch(BranchTransfer $branchTransfer) : string
    {
        return $this
            ->getFactory()
            ->createBranchModel()
            ->getHashForBranch($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositSkuTransfer[]
     */
    public function getDepositSkusForBranch(BranchTransfer $branchTransfer): array
    {
        return $this
            ->getFactory()
            ->createDepositSkuModel()
            ->getDepositSkusForBranch($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param iterable $depositSkus
     * @return void
     */
    public function updateDepositSkus(iterable $depositSkus): void
    {
        $this
            ->getFactory()
            ->createDepositSkuModel()
            ->updateDepositSkus($depositSkus);
    }

    /**
     * {@inheritdoc}
     *
     * @param BranchTransfer $branchTransfer
     * @return DepositSkuTransfer[]
     */
    public function getAcceptedDepositSkusForBranch(BranchTransfer $branchTransfer) : array
    {
        return $this
            ->getFactory()
            ->createDepositSkuModel()
            ->getAcceptedDepositSkusForBranch($branchTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $idDeposit
     *
     * @return DepositSkuTransfer
     */
    public function getDepositSkuByDepositIdForBranch(int $idBranch, int $idDeposit): DepositSkuTransfer
    {
        return $this
            ->getFactory()
            ->createDepositSkuModel()
            ->getDepositSkusByDepositIdForBranch($idBranch, $idDeposit);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $licenseUnits
     * @return void
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     */
    public function sumUpLicenseUnitsToBranchById(int $idBranch, int $licenseUnits): void
    {
        $this
            ->getFactory()
            ->createBranchModel()
            ->sumUpLicenseUnitsToBranchById($idBranch, $licenseUnits);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $merchantPin
     * @return bool
     */
    public function hasMerchantByMerchantPin(string $merchantPin): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->hasMerchantByMerchantPin($merchantPin);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $merchantPin
     * @return bool
     */
    public function hasActiveMerchantByMerchantPin(string $merchantPin): bool
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->hasActiveMerchantByMerchantPin($merchantPin);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserById(int $idBranchUser): BranchUserTransfer
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->getBranchUserById($idBranchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function getBranchUserByEmail(string $email): BranchUserTransfer
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->getBranchUserByEmail($email);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function createBranchUser(BranchUserTransfer $branchUserTransfer): BranchUserTransfer
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->save($branchUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     */
    public function updateBranchUser(BranchUserTransfer $branchUserTransfer): BranchUserTransfer
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->save($branchUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function activateBranchUser(int $idBranchUser): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->activateBranchUser($idBranchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function deactivateBranchUser(int $idBranchUser): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->deactivateBranchUser($idBranchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranchUser
     * @return bool
     */
    public function deleteBranchUser(int $idBranchUser): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->deleteBranchUser($idBranchUser);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return bool
     */
    public function hasActiveBranchUserByEmail(string $email): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->hasActiveBranchUserByEmail($email);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function isValidBranchUserPassword(string $password, string $hash): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->validatePassword(
                $password,
                $hash
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getMerchantByIdBranch(int $idBranch): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantModel()
            ->getMerchantByIdBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function hasCurrentBranchUser(): bool
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->hasCurrentBranchUser();
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return mixed|void
     */
    public function setCurrentBranchUser(BranchUserTransfer $branchUserTransfer)
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->setCurrentBranchUser($branchUserTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\BranchUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchUserNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCurrentBranchUser(): BranchUserTransfer
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->getCurrentBranchUser();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentBranchUser(): void
    {
        $this
            ->getFactory()
            ->createBranchUserModel()
            ->unsetCurrentBranchUser();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentMerchant(): void
    {
        $this
            ->getFactory()
            ->createMerchantModel()
            ->unsetCurrentMerchant();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return BranchUserTransfer[]
     */
    public function getBranchUsersByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createBranchUserModel()
            ->getBranchUsersByIdBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserById(int $idMerchantUser): MerchantUserTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->getMerchantUserById(
                $idMerchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserByEmail(string $email): MerchantUserTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->getMerchantUserByEmail(
                $email
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function createMerchantUser(MerchantUserTransfer $merchantUserTransfer): MerchantUserTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->save(
                $merchantUserTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $branchUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function updateMerchantUser(MerchantUserTransfer $branchUserTransfer): MerchantUserTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->save(
                $branchUserTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function activateMerchantUser(int $idMerchantUser): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->activateMerchantUser(
                $idMerchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function deactivateMerchantUser(int $idMerchantUser): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->deactivateMerchantUser(
                $idMerchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchantUser
     * @return bool
     */
    public function deleteMerchantUser(int $idMerchantUser): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->deleteMerchantUser(
                $idMerchantUser
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $email
     * @return bool
     */
    public function hasActiveMerchantUserByEmail(string $email): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->hasActiveMerchantUserByEmail(
                $email
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function isValidMerchantUserPassword(string $password, string $hash): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->validatePassword(
                $password,
                $hash
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function hasCurrentMerchantUser(): bool
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->hasCurrentMerchantUser();
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return mixed|void
     */
    public function setCurrentMerchantUser(MerchantUserTransfer $merchantUserTransfer)
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->setCurrentMerchantUser(
                $merchantUserTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function getCurrentMerchantUser(): MerchantUserTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->getCurrentMerchantUser();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function unsetCurrentMerchantUser(): void
    {
        $this
            ->getFactory()
            ->createMerchantUserModel()
            ->unsetCurrentMerchantUser();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return array|MerchantUserTransfer[]
     */
    public function getMerchantUsersByIdMerchant(int $idMerchant): array
    {
        return $this
            ->getFactory()
            ->createMerchantUserModel()
            ->getMerchantUsersByIdMerchant(
                $idMerchant
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     * @throws ContainerKeyNotFoundException
     * @throws MerchantNotFoundException
     */
    public function getCurrentUserEmail(): string
    {
        try {
            $currentMerchantUser = $this->getCurrentMerchantUser();

            return $currentMerchantUser->getEmail();
        } catch (MerchantUserNotFoundException $exception) {
            try {
                $currentBranchUser = $this->getCurrentBranchUser();

                return $currentBranchUser->getEmail();
            } catch (BranchUserNotFoundException $exception) {
                $currentMerchant = $this->getCurrentMerchant();

                return $currentMerchant->getMerchantname();
            }
        }
    }
}
