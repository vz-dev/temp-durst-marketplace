<?php
/**
 * Durst - project - CategoryHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 14:17
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Products;


use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CatalogCategoryTransfer;
use Generated\Shared\Transfer\CatalogPriceTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\CatalogUnitTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Controller\MerchantProductsController;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\AbstractProductHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\VersionedHydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductsKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\BranchKeyResponseInterface as Response;
use Pyz\Yves\AppRestApi\Handler\Json\Response\MerchantProductsKeyResponseInterface;
use stdClass;

class CategoriesHydrator extends AbstractProductHydrator implements VersionedHydratorInterface
{
    /**
     * @var string
     */
    protected $version;

    protected $productSkus = [];
    /**
     * CategoriesHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param AppRestApiConfig $config
     */
    public function __construct(
        AppRestApiClientInterface $client,
        AppRestApiConfig $config
    )
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $version
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(string $version, stdClass $requestObject, stdClass $responseObject): void
    {
        $this->version = $version;

        if ($this->version === MerchantProductsController::VERSION_1) {
            $requestKey = MerchantProductsKeyRequestInterface::KEY_MERCHANT_ID;
        }

        if ($this->version === MerchantProductsController::VERSION_2
            || $this->version === MerchantProductsController::VERSION_3
        ) {
            $requestKey = MerchantProductsKeyRequestInterface::KEY_BRANCH_ID;
        }

        $idBranch = $requestObject->{$requestKey};

        if (is_int($idBranch) === false || $idBranch <= 0) {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setBranchIds(
                [
                    $idBranch
                ]
            );

        $responseTransfer = $this
            ->client
            ->getCatalogForBranches(
                $requestTransfer
            );

        $this
            ->hydrateCatalog(
                $responseTransfer->getCategories(),
                $responseObject
            );
    }

    /**
     * @param ArrayObject $categories
     * @param stdClass $responseObject
     * @return void
     */
    protected function hydrateCatalog(ArrayObject $categories, stdClass $responseObject): void
    {
        $responseObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORIES} = [];
        foreach ($categories as $category) {
            if ($category->getImageUrl() == null
                || $category->getImageUrl() == ""
            ) {
               continue;
            }

            $categoryObject = $this
                ->hydrateCategory($category);

            if (count($category->getSubCategory()) != 0
                && $this->version === MerchantProductsController::VERSION_3
            ) {
                $categoryObject->{MerchantProductsKeyResponseInterface::KEY_SUBCATEGORIES} = [];
                foreach ($category->getSubCategory() as $subCategory) {
                    if ($category->getIdCategory() !== $subCategory->getFkParentCategory()) {
                        continue;
                    }
                    $subCategoryObject = $this->hydrateCategory($subCategory);
                    $this
                        ->hydrateProducts(
                            $subCategory,
                            $subCategoryObject,
                            false
                        );
                    $categoryObject->{MerchantProductsKeyResponseInterface::KEY_SUBCATEGORIES}[] = $subCategoryObject;
                }
            }

            $this
                ->hydrateProducts(
                    $category,
                    $categoryObject
                );

            $responseObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORIES}[] = $categoryObject;
        }
    }

    /**
     * @param CatalogCategoryTransfer $category
     * @param stdClass $categoryObject
     * @return void
     */
    protected function hydrateProducts(CatalogCategoryTransfer $category, stdClass $categoryObject, bool $mainCategory = true): void
    {
        $this
            ->orderCategoryProductsByRelevance(
                $category->getProducts()
            );

        $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCTS} = [];

        /* @var $product CatalogProductTransfer */
        foreach ($category->getProducts() as $product) {
            // check for not displaying the product twice in main category and subcategory
            if ($mainCategory === false) {
                $this->productSkus[] = sprintf('%s_%s', $product->getSku(), $category->getFkParentCategory());
            } else {
                if (in_array(sprintf('%s_%s', $product->getSku(), $category->getIdCategory()), $this->productSkus)) {
                    continue;
                }
            }

            $productObject = $this
                ->hydrateProduct($product);
            $this
                ->hydrateUnits(
                    $product->getUnits(),
                    $productObject
                );

            $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCTS}[] = $productObject;
        }
    }

    /**
     * @param ArrayObject $products
     */
    protected function orderCategoryProductsByRelevance(ArrayObject $products): void
    {
        $products->uasort(function (CatalogProductTransfer $firstProduct, CatalogProductTransfer $secondProduct) {
            $firstProductRelevance = $firstProduct->getUnits()[0]->getRelevance();
            $secondProductRelevance = $secondProduct->getUnits()[0]->getRelevance();

            if ($firstProductRelevance < $secondProductRelevance) {
                return 1;
            }

            if ($firstProductRelevance === $secondProductRelevance) {
                return 0;
            }

            return -1;
        });
    }

    /**
     * @param CatalogProductTransfer $productTransfer
     * @return stdClass
     */
    protected function hydrateProduct(CatalogProductTransfer $productTransfer): stdClass
    {
        $productObject = $this->createStdClass();
        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_NAME} = $productTransfer->getName();
        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_SKU} = $productTransfer->getSku();

        if ($this->version === MerchantProductsController::VERSION_1 || $productTransfer->getUrlImageThumb() !== null) {
            $thumbUrl = $this->formatThumb($productTransfer->getUrlImageThumb());

            if ($this->version === MerchantProductsController::VERSION_2
                || $this->version === MerchantProductsController::VERSION_3
            ) {
                $thumbUrl = str_replace($this->config->getMediaServerHost(), '', $thumbUrl);
            }

            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE_THUMB} = $thumbUrl;
        }

        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_TAGS} = $productTransfer->getTags();

        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_MANUFACTURER} = $this->hydrateManufacturer($productTransfer);

        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_RELEVANCE} = $productTransfer->getUnits()[0]->getRelevance();

        if ($this->version === MerchantProductsController::VERSION_1) {
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_IMAGE_BOTTLE} = $this->formatBig(
                $productTransfer->getUrlImageBottle()
            );

            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_LOGO} = $this->formatProductThumb(
                $productTransfer->getUrlProductLogo()
            );

            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_DESCRIPTION} = $productTransfer->getDescription();
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_INGREDIENTS} = $productTransfer->getIngredients();
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_NUTRITIONAL_VALUES} = $productTransfer->getNutritionalValues();
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_ALCOHOL_BY_VOLUME} = $productTransfer->getAlcoholAmount();
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_ALLERGENS} = $productTransfer->getAllergens();
            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_BIO_CONTROL_AUTHORITY} = $productTransfer->getBioControlAuthority();

            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_IMAGE_LIST} = [];

            foreach ($productTransfer->getUrlImageList() as $item) {
                $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_IMAGE_LIST}[] = $this->formatBig($item);
            }
        }

        return $productObject;
    }

    /**
     * @param ArrayObject $units
     * @param stdClass $productObject
     * @return void
     */
    protected function hydrateUnits(ArrayObject $units, stdClass $productObject): void
    {
        $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNITS} = [];

        /* @var $unit CatalogUnitTransfer */
        foreach ($units as $unit) {
            $unitObject = $this
                ->hydrateUnit($unit);
            $this
                ->hydratePrices(
                    $unit
                        ->getPrices(),
                    $unitObject
                );

            $productObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNITS}[] = $unitObject;
        }
    }

    /**
     * @param CatalogUnitTransfer $catalogUnitTransfer
     * @return stdClass
     */
    protected function hydrateUnit(CatalogUnitTransfer $catalogUnitTransfer): stdClass
    {
        $unitObject = $this->createStdClass();

        if ($this->version === MerchantProductsController::VERSION_1 || $catalogUnitTransfer->getDeposit() !== 0) {
            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT} = $catalogUnitTransfer->getDeposit();
        }

        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_WEIGHT} = $catalogUnitTransfer->getWeight();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_NAME} = $catalogUnitTransfer->getName();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_MATERIAL} = $catalogUnitTransfer->getMaterial();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_VOLUME} = $catalogUnitTransfer->getVolume();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_BOTTLE_VOLUME} = $catalogUnitTransfer->getVolumePerBottle();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_BOTTLES} = $catalogUnitTransfer->getBottles();
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_DEPOSIT_TYPE} = $catalogUnitTransfer->getDepositType();

        if ($this->version === MerchantProductsController::VERSION_1) {
            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_CURRENCY} = '€';
            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_SKU} = $catalogUnitTransfer->getSku();
            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRIORITY} = 10;

            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_THUMB} = $this->formatThumb(
                $catalogUnitTransfer->getUrlUnitImageBottle()
            );

            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE} = $this->formatBig(
                $catalogUnitTransfer->getUrlUnitImageBottle()
            );

            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_CASE} = $this->formatBig(
                $catalogUnitTransfer->getUrlUnitImageCase()
            );
        }

        if ($this->version === MerchantProductsController::VERSION_2 || $this->version === MerchantProductsController::VERSION_3) {
            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_CODE} = $catalogUnitTransfer->getCode();

            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_IMAGE_BOTTLE_PLACEHOLDER_URL} = $this->formatImageUrl(
                $catalogUnitTransfer->getUrlUnitImageBottle(), '/resized/{size}'
            );
        }

        return $unitObject;
    }

    /**
     * @param ArrayObject $prices
     * @param stdClass $unitObject
     * @return void
     */
    protected function hydratePrices(ArrayObject $prices, stdClass $unitObject): void
    {
        $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICES} = [];

        /* @var $price CatalogPriceTransfer */
        foreach ($prices as $price) {
            $priceObject = $this
                ->hydratePrice($price);

            $unitObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICES}[] = $priceObject;
        }
    }

    /**
     * @param CatalogPriceTransfer $priceTransfer
     * @return stdClass
     */
    protected function hydratePrice(CatalogPriceTransfer $priceTransfer): stdClass
    {
        $priceObject = $this->createStdClass();

        $priceObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE} = $priceTransfer->getPrice();
        if ($priceTransfer->getStatus() == 'out_of_stock') {
            $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_OUT_OF_STOCK} = true;
        } else {
            $priceObject->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_OUT_OF_STOCK} = false;
        }

        if ($this->version === MerchantProductsController::VERSION_1) {
            $priceObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $priceTransfer->getUnitPrice();
        }

        return $priceObject;
    }

    /**
     * @param CatalogProductTransfer $catalogProductTransfer
     * @return stdClass
     */
    protected function hydrateManufacturer(CatalogProductTransfer $catalogProductTransfer): stdClass
    {
        $manufacturerObject = $this->createStdClass();
        $manufacturerObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_MANUFACTURER_NAME} = $catalogProductTransfer->getManufacturerName();

        if ($this->version === MerchantProductsController::VERSION_1) {
            $manufacturerObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_MANUFACTURER_ADDRESS_1} = $catalogProductTransfer->getManufacturerAddress1();
            $manufacturerObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_MANUFACTURER_ADDRESS_2} = $catalogProductTransfer->getManufacturerAddress2();
        }

        return $manufacturerObject;
    }

    /**
     * @param CatalogCategoryTransfer $catalogCategoryTransfer
     * @return stdClass
     */
    protected function hydrateCategory(CatalogCategoryTransfer $catalogCategoryTransfer): stdClass
    {
        $categoryObject = $this->createStdClass();
        $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_ID} = (string)$catalogCategoryTransfer->getIdCategory();
        $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_NAME} = $catalogCategoryTransfer->getName();

        if ($this->version === MerchantProductsController::VERSION_2 || $this->version === MerchantProductsController::VERSION_3) {
            $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_IMAGE_URL} = $catalogCategoryTransfer->getImageUrl();
            $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_COLOR_CODE} = $catalogCategoryTransfer->getColorCode();
            $categoryObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRIORITY} = $catalogCategoryTransfer->getPriority();
        }

        return $categoryObject;
    }
}
