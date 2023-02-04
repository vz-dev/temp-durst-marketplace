<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Product;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CatalogPriceTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\CatalogUnitTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\AbstractProductHydrator;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\MerchantProductKeyResponseInterface;
use stdClass;

class ProductHydrator extends AbstractProductHydrator implements HydratorInterface
{
    /**
     * @param AppRestApiClientInterface $client
     * @param AppRestApiConfig $config
     */
    public function __construct(AppRestApiClientInterface $client, AppRestApiConfig $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $idBranch = $requestObject->{MerchantProductKeyRequestInterface::KEY_BRANCH_ID};
        $sku = $requestObject->{MerchantProductKeyRequestInterface::KEY_SKU};

        if (is_int($idBranch) === false || $idBranch <= 0 || is_string($sku) === false || $sku === '') {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($idBranch)
            ->setSku($sku);

        $responseTransfer = $this
            ->client
            ->getCatalogProductForBranchBySku($requestTransfer);

        $productObject = $this->hydrateProduct($responseTransfer->getProduct());
        $this->hydrateUnits($responseTransfer->getProduct()->getUnits(), $productObject);

        $responseObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT} = $productObject;
    }

    /**
     * @param CatalogProductTransfer $productTransfer
     * @return stdClass
     */
    protected function hydrateProduct(CatalogProductTransfer $productTransfer): stdClass
    {
        $productObject = $this->createStdClass();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_NAME} = $productTransfer->getName();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_SKU} = $productTransfer->getSku();

        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_IMAGE_BOTTLE_THUMB} = str_replace(
            $this->config->getMediaServerHost(),
            '',
            $this->formatThumb($productTransfer->getUrlImageThumb())
        );

        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_TAGS} = $productTransfer->getTags();

        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_IMAGE_BOTTLE} = str_replace(
            $this->config->getMediaServerHost(),
            '',
            $this->formatBig($productTransfer->getUrlImageBottle())
        );

        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_LOGO} = str_replace(
            $this->config->getMediaServerHost(),
            '',
            $this->formatProductThumb($productTransfer->getUrlProductLogo())
        );

        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_DESCRIPTION} = $productTransfer->getDescription();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_INGREDIENTS} = $productTransfer->getIngredients();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_NUTRITIONAL_VALUES} = $productTransfer->getNutritionalValues();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_MANUFACTURER} = $this->hydrateManufacturer($productTransfer);
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_ALCOHOL_BY_VOLUME} = $productTransfer->getAlcoholAmount();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_ALLERGENS} = $productTransfer->getAllergens();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_BIO_CONTROL_AUTHORITY} = $productTransfer->getBioControlAuthority();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_FAT} = $productTransfer->getFat();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_KILOJOULES} = $productTransfer->getKilojoules();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_INGREDIENTS} = $productTransfer->getIngredients();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_HEREOF_SUGAR} = $productTransfer->getHereofSugar();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_KILOCALORIES} = $productTransfer->getKilocalories();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_CARBOHYDRATES} = $productTransfer->getCarbohydrates();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_HEREOF_SATURATED_FATTY_ACIDS} = $productTransfer->getHereofSaturatedFattyAcids();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_SALT} = $productTransfer->getSalt();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_PROTEINS} = $productTransfer->getProteins();
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_IMAGE_LIST} = [];

        foreach ($productTransfer->getUrlImageList() as $item) {
            $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_IMAGE_LIST}[] = $this->formatBig($item);
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
        $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNITS} = [];

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

            $productObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNITS}[] = $unitObject;
        }
    }

    /**
     * @param CatalogUnitTransfer $catalogUnitTransfer
     * @return stdClass
     */
    protected function hydrateUnit(CatalogUnitTransfer $catalogUnitTransfer): stdClass
    {
        $unitObject = $this->createStdClass();
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_DEPOSIT} = $catalogUnitTransfer->getDeposit();

        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_NAME} = $catalogUnitTransfer->getName();
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_MATERIAL} = $catalogUnitTransfer->getMaterial();
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_VOLUME} = $catalogUnitTransfer->getVolume();

        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_CURRENCY} = '€';
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRIORITY} = 10;
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_BOTTLE_VOLUME} = $catalogUnitTransfer->getVolumePerBottle();
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_BOTTLES} = $catalogUnitTransfer->getBottles();

        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_IMAGE_BOTTLE_PLACEHOLDER_URL} = $this->formatImageUrl(
            $catalogUnitTransfer->getUrlUnitImageBottle(), '/resized/{size}'
        );

        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_IMAGE_CASE} = str_replace(
            $this->config->getMediaServerHost(),
            '',
            $this->formatBig($catalogUnitTransfer->getUrlUnitImageCase())
        );

        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_CODE} = $catalogUnitTransfer->getCode();

        return $unitObject;
    }

    /**
     * @param ArrayObject $prices
     * @param stdClass $unitObject
     * @return void
     */
    protected function hydratePrices(ArrayObject $prices, stdClass $unitObject): void
    {
        $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICES} = [];

        /* @var $price CatalogPriceTransfer */
        foreach ($prices as $price) {
            $priceObject = $this
                ->hydratePrice($price);

            $unitObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICES}[] = $priceObject;
        }
    }

    /**
     * @param CatalogPriceTransfer $priceTransfer
     * @return stdClass
     */
    protected function hydratePrice(CatalogPriceTransfer $priceTransfer): stdClass
    {
        $priceObject = $this->createStdClass();
        $priceObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_PRICE} = $priceTransfer->getPrice();
        $priceObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $priceTransfer->getUnitPrice();

        return $priceObject;
    }

    /**
     * @param CatalogProductTransfer $catalogProductTransfer
     * @return stdClass
     */
    protected function hydrateManufacturer(CatalogProductTransfer $catalogProductTransfer): stdClass
    {
        $manufacturerObject = $this->createStdClass();
        $manufacturerObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_MANUFACTURER_NAME} = $catalogProductTransfer->getManufacturerName();
        $manufacturerObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_MANUFACTURER_ADDRESS_1} = $catalogProductTransfer->getManufacturerAddress1();
        $manufacturerObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT_MANUFACTURER_ADDRESS_2} = $catalogProductTransfer->getManufacturerAddress2();

        return $manufacturerObject;
    }
}
