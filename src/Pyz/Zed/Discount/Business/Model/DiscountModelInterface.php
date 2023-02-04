<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 12:31
 */

namespace Pyz\Zed\Discount\Business\Model;


use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\BranchDiscountTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\SpyDiscountEntityTransfer;
use Generated\Shared\Transfer\SpySalesDiscountEntityTransfer;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface DiscountModelInterface
{
    /**
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     */
    public function getDiscountsByIdBranch(int $idBranch): array;

    /**
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     */
    public function getNonVoucherDiscountsByIdBranch(int $idBranch): array;

    /**
     * @return array
     */
    public function getDiscountsForCurrentBranch(): array;

    /**
     * @param int $idDiscount
     * @return DiscountConfiguratorTransfer
     */
    public function getDiscountConfiguratorTransferById(int $idDiscount): DiscountConfiguratorTransfer;

    /**
     * @param int[] $branchIds
     * @return BranchDiscountTransfer[]
     */
    public function getActiveDiscountsByBranches(array $branchIds): array;

    /**
     * @param int $idBranch
     * @param string $sku
     * @return BranchDiscountTransfer[]
     */
    public function getActiveDiscountsForProduct(int $idBranch, string $sku): array;

    /**
     * @param int $idDiscount
     * @param int $idBranch
     *
     * @return bool
     */
    public function activateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool;

    /**
     * @param string $discountName
     * @return int|null
     */
    public function getIdVoucherDiscountByName(string $discountName): ?int;

    /**
     * @param $discounts
     * @return bool
     */
    public function resetDiscountVouchers($discounts): bool;

    /**
     * @param int $idDiscount
     * @param int $idBranch
     *
     * @return bool
     */
    public function deactivateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool;

    /**
     * @param int $idSalesDiscount
     * @return SpySalesDiscountEntityTransfer
     */
    public function getSalesDiscountById(int $idSalesDiscount): SpySalesDiscountEntityTransfer;

    /**
     * @param SpySalesDiscountEntityTransfer $discountEntityTransfer
     * @return void
     */
    public function updateSalesDiscount(SpySalesDiscountEntityTransfer $discountEntityTransfer): void;

    /**
     * @param int $idBranch
     * @param string $displayName
     * @param string $discountName
     * @param string $sku
     * @param int $amount
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param bool $isActive
     * @return DiscountConfiguratorTransfer
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
    ): DiscountConfiguratorTransfer;

    /**
     * @param int $idDiscount
     * @param int $idBranch
     * @return SpyDiscount
     */
    public function getSpySalesDiscountByIdAndBranch(
        int $idDiscount,
        int $idBranch
    ): SpyDiscount;

    /**
     * @param string $validFrom
     * @param string $validTo
     * @param int $idBranch
     * @return array
     */
    public function getDiscountedSkuForBranchByStartAndEnd(
        string $validFrom,
        string $validTo,
        int $idBranch
    ): array;

    /**
     * @param int $idBranch
     * @param string $sku
     * @return string
     */
    public function getProductNameOfDiscountByBranchId(
        int $idBranch,
        string $sku
    ): string;

    /**
     * @param int $idBranch
     * @param string $sku
     * @return string
     */
    public function getQueryStringForBranchAndSku(
        int $idBranch,
        string $sku
    ): string;

    /**
     * @param int $amount
     * @return ArrayObject|MoneyValueTransfer[]
     */
    public function createMoneyValueCollectionFromAmount(
        int $amount
    ): ArrayObject;

    /**
     * @param int $idDiscountAmount
     * @param int $amount
     * @return ArrayObject|MoneyValueTransfer[]
     */
    public function updateMoneyValueCollectionWithAmount(
        int $idDiscountAmount,
        int $amount
    ): ArrayObject;

    /**
     * @param int $idSalesDiscount
     * @return string
     * @throws AmbiguousComparisonException
     */
    public function findSalesDiscountCode(int $idSalesDiscount): string;
}
