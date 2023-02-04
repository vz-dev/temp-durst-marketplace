<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:34
 */

namespace Pyz\Zed\Discount\Business;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\BranchDiscountTransfer;
use Generated\Shared\Transfer\CartDiscountGroupTransfer;
use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\SpyDiscountEntityTransfer;
use Generated\Shared\Transfer\SpySalesDiscountEntityTransfer;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Model\DiscountDisplayNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface;
use Spryker\Zed\Discount\Business\DiscountFacadeInterface as SprykerDiscountFacadeInterface;
use Throwable;

interface DiscountFacadeInterface extends SprykerDiscountFacadeInterface
{
    /**
     * Fetch all discounts for a specific branch, identified by its id
     *
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     */
    public function getDiscountsByIdBranch(int $idBranch): array;

    /**
     * Fetch all non voucher discounts for a specific branch, identified by its id
     *
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     */
    public function getNonVoucherDiscountsByIdBranch(int $idBranch): array;

    /**
     * Get an instance of the display name generator for use in Merchant Center
     *
     * @return DiscountDisplayNameGeneratorInterface
     */
    public function getDiscountDisplayNameGenerator(): DiscountDisplayNameGeneratorInterface;

    /**
     * Get an instance of the cart discount group name generator for use in Merchant Center
     *
     * @return CartDiscountGroupNameGeneratorInterface
     */
    public function getCartDiscountGroupNameGenerator(): CartDiscountGroupNameGeneratorInterface;

    /**
     * Check, if the correct branch for the discount is used
     *
     * @param QuoteTransfer $quoteTransfer
     * @param ItemTransfer $itemTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return bool
     */
    public function isBranchSatisfiedBy(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer, ClauseTransfer $clauseTransfer): bool;

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return array
     */
    public function collectByBranch(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): array;

    /**
     * Get a discount by its id
     *
     * @param int $idDiscount
     * @return DiscountConfiguratorTransfer
     */
    public function getDiscountConfiguratorTransferById(int $idDiscount): DiscountConfiguratorTransfer;

    /**
     * Get a list of all active discounts for given branches
     *
     * @param int[] $branchIds
     * @return BranchDiscountTransfer[]
     */
    public function getActiveDiscountsByBranches(array $branchIds): array;

    /**
     * Retrieve all active discounts for the product identified by the given branch ID and SKU.
     *
     * @param int $idBranch
     * @param string $sku
     * @return BranchDiscountTransfer[]
     */
    public function getActiveDiscountsForProduct(int $idBranch, string $sku): array;

    /**
     * Fetch a discount by its id and a given branch
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return DiscountConfiguratorTransfer
     */
    public function getHydratedDiscountConfiguratorByIdDiscountAndIdBranch(int $idDiscount, int $idBranch): DiscountConfiguratorTransfer;

    /**
     * Set active flag to TRUE for a given discount id with the corresponding branch
     * for discounts that are not of the type "voucher"
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     */
    public function activateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool;
    
    /**
     * Updates the number of usages for the discount by discount id
     *
     * @param iterable $discounts
     * @return bool
     */
    public function resetDiscountVouchers(iterable $discounts): bool;

    /**
     * Set active flag to FALSE for a given discount id with the corresponding branch,
     * for discounts that are not of the type "voucher"
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     */
    public function deactivateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool;

    /**
     * Extract all needed information from the request object and create a quote transfer from it
     * Next, add a voucher discount to the generated quote
     *
     * @param DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return QuoteTransfer
     */
    public function addVoucherCodeToQuote(DiscountApiRequestTransfer $discountApiRequestTransfer): QuoteTransfer;

    /**
     * Adds global voucher (discount) expense to sales order.
     *
     * @param QuoteTransfer $quoteTransfer
     * @param SaveOrderTransfer $saveOrderTransfer
     * @return void
     * @throws Throwable
     */
    public function saveOrderGlobalVoucher(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void;

    /**
     * Get a fully hydrated cart discount group by its id
     * And the corresponding branch
     *
     * @param int $idCartDiscountGroup
     * @param int $idBranch
     * @return CartDiscountGroupTransfer
     */
    public function getCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): CartDiscountGroupTransfer;

    /**
     * Get a list of all cart discount groups for the given branch
     *
     * @param int $idBranch
     * @return array|CartDiscountGroupTransfer[]
     */
    public function getCartDiscountGroupsByBranch(
        int $idBranch
    ): array;

    /**
     * Generate cart discount groups from (old) existing discounts
     *
     * @return array|CartDiscountGroupTransfer[]
     */
    public function generateCartDiscountGroups(): array;

    /**
     * Get a sales discount by its id
     *
     * @param int $idSalesDiscount
     * @return SpySalesDiscountEntityTransfer
     */
    public function getSalesDiscountById(int $idSalesDiscount): SpySalesDiscountEntityTransfer;

    /**
     * Get a sales discount by its id
     *
     * @param int $idSalesDiscount
     * @return string
     */
    public function getSalesDiscountCodeById(int $idSalesDiscount): string;

    /**
     * Update the given sales discount transfer in the database
     *
     * @param SpySalesDiscountEntityTransfer $discountEntityTransfer
     * @return void
     */
    public function updateSalesDiscount(SpySalesDiscountEntityTransfer $discountEntityTransfer): void;

    /**
     * Expose the list of calculator plugins
     *
     * @return array
     */
    public function getCalculatorPlugins(): array;

    /**
     * Return a discount configurator transfer created by the main data needed
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
     * Get a SpySalesDiscount by its ID for the given branch
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return SpyDiscount
     */
    public function getSpySalesDiscountByIdAndBranch(
        int $idDiscount,
        int $idBranch
    ): SpyDiscount;

    /**
     * Get a list of all SKUs for the given branch that are already part of discounts
     * Between the start and end date
     *
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
     * Get a list of all suitable validators for a cart discount group
     *
     * @return array|CartDiscountGroupValidatorInterface[]
     */
    public function getCartDiscountGroupValidators(): array;

    /**
     * Get the product name of the discount by sku and branch id
     *
     * @param int $branchId
     * @param string $sku
     * @return string
     */
    public function getProductNameOfDiscountByBranchId(int $branchId, string $sku): string;

    /**
     * Get the query string with branch and sku set
     *
     * @param int $idBranch
     * @param string $sku
     * @return string
     */
    public function getQueryStringForBranchAndSku(
        int $idBranch,
        string $sku
    ): string;

    /**
     * Create a money value collection from the given amount
     *
     * @param int $amount
     * @return ArrayObject|MoneyValueTransfer[]
     */
    public function createMoneyValueCollectionFromAmount(
        int $amount
    ): ArrayObject;

    /**
     * Get an updated money value collection where the money value with the given ID
     * is updated with the new given amount
     *
     * @param int $idDiscountAmount
     * @param int $amount
     * @return ArrayObject|MoneyValueTransfer[]
     */
    public function updateMoneyValueCollectionWithAmount(
        int $idDiscountAmount,
        int $amount
    ): ArrayObject;
}
