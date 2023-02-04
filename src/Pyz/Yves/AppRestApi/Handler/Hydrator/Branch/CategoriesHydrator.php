<?php
/**
 * Durst - project - CategoriesHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 16:20
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Branch;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CatalogCategoryTransfer;
use Generated\Shared\Transfer\CatalogPriceTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\CatalogUnitTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\BranchKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\BranchKeyResponseInterface as Response;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class CategoriesHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var \Spryker\Yves\Money\Plugin\MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * CategoriesHydrator constructor.
     *
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $client
     * @param \Spryker\Yves\Money\Plugin\MoneyPlugin $moneyPlugin
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     */
    public function __construct(AppRestApiClientInterface $client, MoneyPlugin $moneyPlugin, AppRestApiConfig $config)
    {
        $this->client = $client;
        $this->moneyPlugin = $moneyPlugin;
        $this->config = $config;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return mixed|void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        if ($responseObject->{Response::KEY_ZIP_CODE_MERCHANTS_FOUND} !== true) {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setBranchIds($this->getBranchIds($requestObject, $responseObject));

        $responseTransfer = $this
            ->client
            ->getCatalogForBranches($requestTransfer);

        $this->hydrateCatalog($responseTransfer->getCategories(), $responseObject);
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogCategoryTransfer[]|\ArrayObject $categories
     * @param \stdClass $responseObject
     *
     * @return void
     */
    protected function hydrateCatalog($categories, stdClass $responseObject)
    {
        $responseObject->{Response::KEY_CATEGORIES} = [];
        foreach ($categories as $category) {
            $categoryObject = $this->hydrateCategory($category);
            $this->hydrateProducts($category->getProducts(), $categoryObject);

            $responseObject->{Response::KEY_CATEGORIES}[] = $categoryObject;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogProductTransfer[]|\ArrayObject $products
     * @param \stdClass $categoryObject
     *
     * @return void
     */
    protected function hydrateProducts($products, stdClass $categoryObject)
    {
        $this->orderCategoryProductsByName($products);

        $categoryObject->{Response::KEY_CATEGORY_PRODUCTS} = [];
        foreach ($products as $product) {
            $productObject = $this->hydrateProduct($product);
            $this->hydrateUnits($product->getUnits(), $productObject);

            $categoryObject->{Response::KEY_CATEGORY_PRODUCTS}[] = $productObject;
        }
    }

    /**
     * @param \ArrayObject $products
     *
     * @return void
     */
    protected function orderCategoryProductsByName(ArrayObject $products)
    {
        $products->uasort(function (CatalogProductTransfer $firstProduct, CatalogProductTransfer $secondProduct) {

            $firstProductName = strtolower($firstProduct->getName());
            $secondProductName = strtolower($secondProduct->getName());

            if ($firstProductName > $secondProductName) {
                return 1;
            }

            if ($firstProductName === $secondProductName) {
                return 0;
            }

            return -1;
        });
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogUnitTransfer[] $units
     * @param \stdClass $productObject
     *
     * @return void
     */
    protected function hydrateUnits($units, stdClass $productObject)
    {
        $productObject->{Response::KEY_CATEGORY_PRODUCT_UNITS} = [];
        foreach ($units as $unit) {
            $unitObject = $this->hydrateUnit($unit);
            $this->hydratePrices($unit->getPrices(), $unitObject);

            $productObject->{Response::KEY_CATEGORY_PRODUCT_UNITS}[] = $unitObject;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogPriceTransfer[] $prices
     * @param \stdClass $unitObject
     *
     * @return void
     */
    protected function hydratePrices($prices, stdClass $unitObject)
    {
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICES} = [];
        foreach ($prices as $price) {
            $priceObject = $this->hydratePrice($price);

            $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICES}[] = $priceObject;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogPriceTransfer $priceTransfer
     *
     * @return \stdClass
     */
    protected function hydratePrice(CatalogPriceTransfer $priceTransfer): stdClass
    {
        $priceObject = $this->createStdClass();
        $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_MERCHANT_ID} = $priceTransfer->getIdBranch();
        $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_OUT_OF_STOCK} = false;
        $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE} = $this->formatMoney($priceTransfer->getPrice());
        $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $this->formatMoney($priceTransfer->getUnitPrice());

        return $priceObject;
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogUnitTransfer $unitTransfer
     *
     * @return \stdClass
     */
    protected function hydrateUnit(CatalogUnitTransfer $unitTransfer): stdClass
    {
        $unitObject = $this->createStdClass();
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_CURRENCY} = '€';
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT} = $this->formatMoney($unitTransfer->getDeposit());
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_DISCOUNT} = 0;
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_NAME} = $unitTransfer->getName();
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_SKU} = $unitTransfer->getSku();
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRIORITY} = 10;
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_ATTRIBUTE_VOLUME} = $unitTransfer->getVolume();
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_THUMB} = $this->formatUnitThumb($unitTransfer->getUrlUnitImageBottle());
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE} = $this->formatUnitBig($unitTransfer->getUrlUnitImageBottle());
        $unitObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_CASE} = $this->formatUnitBig($unitTransfer->getUrlUnitImageCase());

        return $unitObject;
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogProductTransfer $productTransfer
     *
     * @return \stdClass
     */
    protected function hydrateProduct(CatalogProductTransfer $productTransfer): stdClass
    {
        $productObject = $this->createStdClass();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_NAME} = $productTransfer->getName();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_SKU} = $productTransfer->getSku();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE} = $this->formatBig($productTransfer->getUrlImageBottle());
        $productObject->{Response::KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE_THUMB} = $this->formatThumb($productTransfer->getUrlImageThumb());
        $productObject->{Response::KEY_CATEGORY_PRODUCT_LOGO} = $this->formatThumb($productTransfer->getUrlProductLogo());
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_DESCRIPTION} = $productTransfer->getDescription();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_INGREDIENTS} = $productTransfer->getIngredients();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_NUTRITIONAL_VALUES} = $productTransfer->getNutritionalValues();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER} = $this->hydrateManufacturer($productTransfer);
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ALCOHOL_BY_VOLUME} = $productTransfer->getAlcoholAmount();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_ALLERGENS} = $productTransfer->getAllergens();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_TAGS} = $productTransfer->getTags();
        $productObject->{Response::KEY_CATEGORY_PRODUCT_BIO_CONTROL_AUTHORITY} = $productTransfer->getBioControlAuthority();

        $productObject->{Response::KEY_CATEGORY_PRODUCT_IMAGE_LIST} = [];
        foreach ($productTransfer->getUrlImageList() as $image) {
            $productObject->{Response::KEY_CATEGORY_PRODUCT_IMAGE_LIST}[] = $this->formatBig($image);
        }

        return $productObject;
    }

    /**
     * @param string|null $url
     * @param string $host
     *
     * @return string
     */
    protected function formatImageUrl(?string $url, string $host): string
    {
        if ($url === null || $url === '') {
            return $this->formatImageUrl($this->config->getFallbackImageProduct(), $host);
        }

        return sprintf(
            '%s/%s',
            $host,
            $url
        );
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    protected function formatThumb(?string $url = null): string
    {
        return $this
            ->formatImageUrl($url, $this->config->getThumbImageHost());
    }

    /**
     * @param string|null $url
     *
     * @return string
     */
    protected function formatBig(?string $url = null): string
    {
        return $this
            ->formatImageUrl($url, $this->config->getBigImageHost());
    }

    /**
     * @param string|null $url
     * @return string|null
     */
    protected function formatUnitThumb(?string $url = null)
    {
        if ($url === null || $url === '') {
            return null;
        }

        return sprintf(
            '%s/%s',
            $this->config->getThumbImageHost(),
            $url
        );
    }

    /**
     * @param string|null $url
     * @return string|null
     */
    protected function formatUnitBig(?string $url = null)
    {
        if ($url === null || $url === '') {
            return null;
        }

        return sprintf(
            '%s/%s',
            $this->config->getBigImageHost(),
            $url
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogProductTransfer $productTransfer
     *
     * @return \stdClass
     */
    protected function hydrateManufacturer(CatalogProductTransfer $productTransfer): stdClass
    {
        $manufacturerObject = $this->createStdClass();
        $manufacturerObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_NAME} = $productTransfer->getManufacturerName();
        $manufacturerObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_ADDRESS_1} = $productTransfer->getManufacturerAddress1();
        $manufacturerObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_ADDRESS_2} = $productTransfer->getManufacturerAddress2();

        if ($productTransfer->getManufacturerLogoUrl() !== null && $productTransfer->getManufacturerLogoUrl() !== '') {
            $manufacturerObject->{Response::KEY_CATEGORY_PRODUCT_ATTRIBUTE_MANUFACTURER_IMAGE} = $this->formatThumb($productTransfer->getManufacturerLogoUrl());
        }

        return $manufacturerObject;
    }

    /**
     * @param \Generated\Shared\Transfer\CatalogCategoryTransfer $categoryTransfer
     *
     * @return \stdClass
     */
    protected function hydrateCategory(CatalogCategoryTransfer $categoryTransfer): stdClass
    {
        $categoryObject = $this->createStdClass();
        $categoryObject->{Response::KEY_CATEGORY_ID} = $categoryTransfer->getIdCategory();
        $categoryObject->{Response::KEY_CATEGORY_NAME} = $categoryTransfer->getName();

        return $categoryObject;
    }

    /**
     * @return \stdClass
     */
    protected function createStdClass(): stdClass
    {
        return new stdClass();
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return array
     */
    protected function getBranchIds(stdClass $requestObject, stdClass $responseObject): array
    {
        if ($requestObject->{Request::KEY_MERCHANT_ID} > 0) {
            return [
                $requestObject->{Request::KEY_MERCHANT_ID},
            ];
        }

        $branchIds = [];
        foreach ($responseObject->{Response::KEY_MERCHANTS} as $merchant) {
            $branchIds[] = $merchant->{Response::KEY_MERCHANTS_ID};
        }

        return $branchIds;
    }

    /**
     * @param int|null $amount
     *
     * @return float|null
     */
    protected function formatMoney(?int $amount = null)
    {
        if ($amount === null) {
            return null;
        }
        return $this->moneyPlugin->convertIntegerToDecimal($amount);
    }
}
