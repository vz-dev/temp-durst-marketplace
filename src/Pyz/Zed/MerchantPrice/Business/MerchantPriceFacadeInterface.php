<?php

namespace Pyz\Zed\MerchantPrice\Business;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CatalogCategoryTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\PriceTransfer;
use Pyz\Zed\MerchantPrice\Business\Exception\WrongBranchException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

interface MerchantPriceFacadeInterface
{
    /**
     * Updates a existing price in the database and returns a
     * newly hydrated transfer object
     *
     * @param PriceTransfer $priceTransfer
     * @return PriceTransfer
     */
    public function updatePrice(PriceTransfer $priceTransfer);

    /**
     * Updates existing price or creates a new entity based on PriceTransfer
     * provided by importer. Comparision is done via sku and branch id
     *
     * @param PriceTransfer $priceTransfer
     * @return PriceTransfer
     */
    public function importPrice(PriceTransfer $priceTransfer);

    /**
     * Returns a fully hydrated transfer object representing the price
     * matching the branch and product id. If no price with these ids can be
     * found an exception will be thrown
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return PriceTransfer
     */
    public function getPriceByIdBranchAndIdProduct($idBranch, $idProduct);

    /**
     * Checks whether a price exists for the given ids for branch and product.
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return bool
     */
    public function hasPriceByIdBranchAndIdProduct($idBranch, $idProduct);

    /**
     * Remove the price matching the given id from the database. As a safety mechanism
     * the branch id of the given price will be checked to make sure one doesn't delete a
     * price from a different branch.
     *
     *
     * @throws WrongBranchException if the current branch id and the branch id of the price with the
     * given id don't match
     * @param $idPrice
     * @return void
     */
    public function removePrice($idPrice);

    /**
     * Checks if there is a price in the database matching the given id
     *
     * @param int $idPrice
     * @return bool
     */
    public function hasPriceById($idPrice);

    /**
     * Hydrates the items in the cart with prices for the specific branch
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function addPriceToItem(CartChangeTransfer $cartChangeTransfer) : CartChangeTransfer;

    /**
     * Receives all categories, products, units and prices for the given branches.
     *
     * @param array $branchIds
     * @return CatalogCategoryTransfer[]|\ArrayObject
     */
    public function getCatalogForBranches(array $branchIds);

    /**
     * Adds a total gross subtotal representing the gross price minus all expenses.
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function recalculateGrossSubtotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Build a page map for transferring Propel entity MerchantPrice to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $priceData
     * @param LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $priceData, LocaleTransfer $localeTransfer) : PageMapTransfer;

    /**
     * Get all prices for the given branch
     *
     * @param int $idBranch
     * @return PriceTransfer[]
     */
    public function getPricesForBranch(int $idBranch): array;

    /**
     * Import the given price transfer for a branch
     *
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     */
    public function importPriceForBranch(PriceTransfer $priceTransfer);

    /**
     * Remove a specific price identified by its id from the given branch
     *
     * @param int $idPrice
     * @param int $idBranch
     */
    public function removePriceFromBranch(
        int $idPrice,
        int $idBranch
    );

    /**
     * Retrieves the product and its units and prices identified by the given branch ID and SKU.
     *
     * @param int $idBranch
     * @param string $sku
     * @param bool $deactivated
     *
     * @return CatalogProductTransfer
     */
    public function getCatalogProductForBranchBySku(int $idBranch, string $sku, string $concreteSku = null, bool $deactivated = false, bool $archived = false): CatalogProductTransfer;

    /**
     * Retrieves the price identified by the given branch ID and product ID.
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return PriceTransfer
     */
    public function getCampaignPriceForBranchByConcreteSku(
        int $idBranch,
        int $idProduct
    ): PriceTransfer;

    /**
     * Identifies if the price is active or not by the given product sku
     *
     * @param string $sku
     * @return array
     */
    public function getIdBranchForActivePrice(
        string $sku
    ): array;

    /**
     * This will add the count of the items sold for the last month
     *
     * @see \Pyz\Shared\DeliveryArea\DeliveryAreaConstants::CONCRETE_TIME_SLOT_CREATION_LIMIT
     *
     * @return void
     */
    public function createCountItems();
}
