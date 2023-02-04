<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:35
 */

namespace Pyz\Zed\Discount\Business;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\CartDiscountGroupTransfer;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\SpyDiscountEntityTransfer;
use Generated\Shared\Transfer\SpySalesDiscountEntityTransfer;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Model\DiscountDisplayNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface;
use Spryker\Zed\Discount\Business\DiscountFacade as SprykerDiscountFacade;
use Spryker\Zed\Discount\Business\Exception\ComparatorException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Throwable;

/**
 * Class DiscountFacade
 * @package Pyz\Zed\Discount\Business
 * @method DiscountBusinessFactory getFactory()
 */
class DiscountFacade extends SprykerDiscountFacade implements DiscountFacadeInterface
{

    /**
     * @inheritdoc}
     *
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     * @throws ContainerKeyNotFoundException
     */
    public function getDiscountsByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getDiscountsByIdBranch($idBranch);
    }

    /**
     * @inheritdoc}
     *
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     * @throws ContainerKeyNotFoundException
     */
    public function getNonVoucherDiscountsByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getNonVoucherDiscountsByIdBranch($idBranch);
    }

    /**
     * {@inheritdoc}
     *
     * @return DiscountDisplayNameGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getDiscountDisplayNameGenerator(): DiscountDisplayNameGeneratorInterface
    {
        return $this
            ->getFactory()
            ->createDiscountDisplayNameGenerator();
    }

    /**
     * {@inheritDoc}
     *
     * @return CartDiscountGroupNameGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getCartDiscountGroupNameGenerator(): CartDiscountGroupNameGeneratorInterface
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupNameGenerator();
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function calculateDiscounts(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createDiscount()
            ->calculate($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ItemTransfer $itemTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return bool
     * @throws ComparatorException
     */
    public function isBranchSatisfiedBy(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer, ClauseTransfer $clauseTransfer): bool
    {
        return $this
            ->getFactory()
            ->createBranchDecisionRule()
            ->isSatisfiedBy(
                $quoteTransfer,
                $itemTransfer,
                $clauseTransfer
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return array
     * @throws ComparatorException
     */
    public function collectByBranch(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): array
    {
        return $this
            ->getFactory()
            ->createBranchCollector()
            ->collect(
                $quoteTransfer,
                $clauseTransfer
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDiscount
     * @return DiscountConfiguratorTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getDiscountConfiguratorTransferById(int $idDiscount): DiscountConfiguratorTransfer
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getDiscountConfiguratorTransferById($idDiscount);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return array
     * @throws ContainerKeyNotFoundException
     */
    public function getActiveDiscountsByBranches(array $branchIds): array
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getActiveDiscountsByBranches($branchIds);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContainerKeyNotFoundException
     */
    public function getActiveDiscountsForProduct(int $idBranch, string $sku): array
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getActiveDiscountsForProduct($idBranch, $sku);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return DiscountConfiguratorTransfer
     * @throws AmbiguousComparisonException
     */
    public function getHydratedDiscountConfiguratorByIdDiscountAndIdBranch(int $idDiscount, int $idBranch): DiscountConfiguratorTransfer
    {
        return $this
            ->getFactory()
            ->createDiscountConfiguratorHydrate()
            ->getByIdDiscountAndIdBranch(
                $idDiscount,
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function activateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->activateDiscountByIdDiscountForBranchId($idDiscount, $idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param iterable $discounts
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function resetDiscountVouchers(iterable $discounts): bool
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->resetDiscountVouchers($discounts);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     * @throws ContainerKeyNotFoundException
     */
    public function deactivateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->deactivateDiscountByIdDiscountForBranchId($idDiscount, $idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return QuoteTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function addVoucherCodeToQuote(DiscountApiRequestTransfer $discountApiRequestTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createVoucherModel()
            ->addVoucherCodeToQuote(
                $discountApiRequestTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws Throwable
     */
    public function saveOrderGlobalVoucher(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void
    {
        $this
            ->getFactory()
            ->createGlobalVoucherOrderSaver()
            ->saveOrderGlobalVoucher(
                $quoteTransfer,
                $saveOrderTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCartDiscountGroup
     * @return CartDiscountGroupTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): CartDiscountGroupTransfer
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupModel()
            ->getCartDiscountGroupById(
                $idCartDiscountGroup,
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return array
     * @throws ContainerKeyNotFoundException
     */
    public function getCartDiscountGroupsByBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupModel()
            ->getCartDiscountGroupsByBranch(
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array|CartDiscountGroupTransfer[]
     * @throws ContainerKeyNotFoundException
     */
    public function generateCartDiscountGroups(): array
    {
        return $this
            ->getFactory()
            ->createCartDiscountGroupModel()
            ->generateCartDiscountGroups();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesDiscount
     * @return SpySalesDiscountEntityTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getSalesDiscountById(int $idSalesDiscount): SpySalesDiscountEntityTransfer
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getSalesDiscountById(
                $idSalesDiscount
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesDiscount
     * @return string
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function getSalesDiscountCodeById(int $idSalesDiscount): string
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->findSalesDiscountCode(
                $idSalesDiscount
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SpySalesDiscountEntityTransfer $discountEntityTransfer
     * @return void
     * @throws ContainerKeyNotFoundException
     */
    public function updateSalesDiscount(SpySalesDiscountEntityTransfer $discountEntityTransfer): void
    {
        $this
            ->getFactory()
            ->createDiscountModel()
            ->updateSalesDiscount(
                $discountEntityTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getCalculatorPlugins(): array
    {
        return $this
            ->getFactory()
            ->getCalculatorPlugins();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $displayName
     * @param string $discountName
     * @param string $sku
     * @param int $amount
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param bool $isActive
     * @return DiscountConfiguratorTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function createDiscountConfiguratorTransfer(
        int $idBranch,
        string $displayName,
        string $discountName,
        string $sku,
        int $amount,
        DateTime $validFrom,
        DateTime $validTo,
        bool $isActive = false
    ): DiscountConfiguratorTransfer
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->createDiscountConfiguratorTransfer(
                $idBranch,
                $displayName,
                $discountName,
                $sku,
                $amount,
                $validFrom,
                $validTo,
                $isActive
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return SpyDiscount
     * @throws ContainerKeyNotFoundException
     */
    public function getSpySalesDiscountByIdAndBranch(
        int $idDiscount,
        int $idBranch
    ): SpyDiscount
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getSpySalesDiscountByIdAndBranch(
                $idDiscount,
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $validFrom
     * @param string $validTo
     * @param int $idBranch
     * @return array
     * @throws ContainerKeyNotFoundException
     */
    public function getDiscountedSkuForBranchByStartAndEnd(
        string $validFrom,
        string $validTo,
        int $idBranch
    ): array
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getDiscountedSkuForBranchByStartAndEnd(
                $validFrom,
                $validTo,
                $idBranch
            );
    }

    /**
     * @return array|CartDiscountGroupValidatorInterface[]
     * @throws ContainerKeyNotFoundException
     */
    public function getCartDiscountGroupValidators(): array
    {
        return $this
            ->getFactory()
            ->getCartDiscountGroupValidators();
    }


    /**
     * @param int $branchId
     * @param string $sku
     * @return string
     */
    public function getProductNameOfDiscountByBranchId(int $branchId, string $sku): string {

        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getProductNameOfDiscountByBranchId(
                $branchId,
                $sku
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $sku
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    public function getQueryStringForBranchAndSku(
        int $idBranch,
        string $sku
    ): string
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->getQueryStringForBranchAndSku(
                $idBranch,
                $sku
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $amount
     * @return ArrayObject
     * @throws ContainerKeyNotFoundException
     */
    public function createMoneyValueCollectionFromAmount(
        int $amount
    ): ArrayObject
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->createMoneyValueCollectionFromAmount(
                $amount
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscountAmount
     * @param int $amount
     * @return ArrayObject
     * @throws ContainerKeyNotFoundException
     */
    public function updateMoneyValueCollectionWithAmount(
        int $idDiscountAmount,
        int $amount
    ): ArrayObject
    {
        return $this
            ->getFactory()
            ->createDiscountModel()
            ->updateMoneyValueCollectionWithAmount(
                $idDiscountAmount,
                $amount
            );
    }
}
