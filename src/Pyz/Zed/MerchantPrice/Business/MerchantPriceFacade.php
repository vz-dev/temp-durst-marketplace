<?php

namespace Pyz\Zed\MerchantPrice\Business;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\PriceTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\MerchantPrice\Business\Exception\WrongBranchException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * @method \Pyz\Zed\MerchantPrice\Business\MerchantPriceBusinessFactory getFactory()
 */
class MerchantPriceFacade extends AbstractFacade implements MerchantPriceFacadeInterface
{
    /**
     * @inheritdoc}
     *
     * @param PriceTransfer $priceTransfer
     * @return PriceTransfer | bool
     * @throws Exception\PriceNotFoundException
     * @throws Exception\ProductNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updatePrice(PriceTransfer $priceTransfer)
    {
        return $this->getFactory()
            ->createPriceModel()
            ->save($priceTransfer);
    }

    /**
     * @inheritdoc}
     *
     * @param PriceTransfer $priceTransfer
     * @return PriceTransfer
     * @throws Exception\PriceNotFoundException
     * @throws Exception\ProductNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function importPrice(PriceTransfer $priceTransfer)
    {
        return $this->getFactory()
            ->createPriceModel()
            ->import($priceTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return PriceTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws Exception\PriceNotFoundException
     */
    public function getPriceByIdBranchAndIdProduct($idBranch, $idProduct)
    {
        return $this
            ->getFactory()
            ->createPriceModel()
            ->getPriceByIdBranchAndIdProduct($idBranch, $idProduct);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasPriceByIdBranchAndIdProduct($idBranch, $idProduct)
    {
        return $this
            ->getFactory()
            ->createPriceModel()
            ->hasPriceByIdBranchAndIdProduct($idBranch, $idProduct);
    }

    /**
     * {@inheritdoc}
     *
     * @throws WrongBranchException
     * @param $idPrice
     * @return void
     * @throws \Exception
     */
    public function removePrice($idPrice)
    {
        $this
            ->getFactory()
            ->createPriceModel()
            ->removePrice($idPrice);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idPrice
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hasPriceById($idPrice)
    {
        return $this
            ->getFactory()
            ->createPriceModel()
            ->hasPriceById($idPrice);
    }

    /**
     * {@inheritdoc}
     *
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     * @throws Exception\PriceMissingException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function addPriceToItem(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this
            ->getFactory()
            ->createPriceManager()
            ->addPriceToItems($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $branchIds
     * @return \ArrayObject|\Generated\Shared\Transfer\CatalogCategoryTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCatalogForBranches(array $branchIds)
    {
        return $this
            ->getFactory()
            ->createCatalog()
            ->getCatalogForBranches($branchIds);
    }

    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculateGrossSubtotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createMerchantPriceCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $priceData
     * @param LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $priceData, LocaleTransfer $localeTransfer) : PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createPriceDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $priceData, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return PriceTransfer[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getPricesForBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createPriceExportModel()
            ->getPricesForBranch(
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\PriceTransfer $priceTransfer
     * @return \Generated\Shared\Transfer\PriceTransfer | bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function importPriceForBranch(PriceTransfer $priceTransfer)
    {
        return $this
            ->getFactory()
            ->createPriceImportModel()
            ->importPriceForBranch(
                $priceTransfer
            );
    }

    /**
     * Identifies if the price is active or not by the given product sku
     *
     * @param string $sku
     * @return array
     */
    public function getIdBranchForActivePrice(
        string $sku
    ): array {
        return $this
            ->getFactory()
            ->createPriceModel()
            ->getIdBranchForActivePrice(
                $sku
            );
    }

    /**
     * @param int $idPrice
     * @param int $idBranch
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function removePriceFromBranch(
        int $idPrice,
        int $idBranch
    )
    {
        $this
            ->getFactory()
            ->createPriceImportModel()
            ->removePriceFromBranch(
                $idPrice,
                $idBranch
            );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getCatalogProductForBranchBySku(int $idBranch, string $sku,  string $concreteSku = null, bool $deactivated = false, bool $archived = false): CatalogProductTransfer
    {
        return $this
            ->getFactory()
            ->createCatalog()
            ->getCatalogProductForBranchBySku($idBranch, $sku, $concreteSku, $deactivated, $archived);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param int $idProduct
     * @return \Generated\Shared\Transfer\PriceTransfer
     * @throws \Pyz\Zed\MerchantPrice\Business\Exception\PriceNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCampaignPriceForBranchByConcreteSku(
        int $idBranch,
        int $idProduct
    ): PriceTransfer
    {
        return $this
            ->getFactory()
            ->createCampaignPriceModel()
            ->getPriceByIdBranchAndIdProduct(
                $idBranch,
                $idProduct
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function createCountItems()
    {
        return $this
            ->getFactory()
            ->createItemModel()
            ->countSoldItems();
    }
}
