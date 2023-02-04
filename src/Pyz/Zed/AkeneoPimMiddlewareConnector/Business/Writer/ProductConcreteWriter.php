<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */


namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Writer;

use Orm\Zed\Currency\Persistence\SpyCurrencyQuery;
use Orm\Zed\PriceProduct\Persistence\Map\SpyPriceTypeTableMap;
use Orm\Zed\PriceProduct\Persistence\SpyPriceProductQuery;
use Orm\Zed\PriceProduct\Persistence\SpyPriceProductStoreQuery;
use Orm\Zed\PriceProduct\Persistence\SpyPriceTypeQuery;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Orm\Zed\Store\Persistence\Base\SpyStoreQuery;
use Pyz\Shared\Product\ProductConstants;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\UnitRepository;
use Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository;
use Pyz\Zed\DataImport\Business\Model\ProductConcrete\ProductConcreteWriter as DataImportProductConcreteWriter;
use Spryker\Zed\Availability\Business\AvailabilityFacadeInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\DataImport\Dependency\Facade\DataImportToTouchInterface;
use Spryker\Zed\Stock\StockConfig;

class ProductConcreteWriter extends DataImportProductConcreteWriter
{
    const KEY_PRICES = 'prices';
    const KEY_PRICE = 'price';
    const KEY_CURRENCY = 'currency';
    const KEY_PRICE_TYPE = 'type';
    const KEY_STORE = 'store';

    const ATTRIBUTES_KEY_UNIT = 'unit';

    const DEFAULT_PRICE_TYPE = 'DEFAULT';
    const DEFAULT_STORE = 'DE';
    const DEFAULT_CURRENCY = 'EUR';
    const DEFAULT_PRICE = 1000;
    const DEFAULT_STOCK = 'Warehouse1';

    /**
     * @var \Orm\Zed\Currency\Persistence\SpyCurrency[]
     */
    protected static $currencyCache = [];

    /**
     * @var \Orm\Zed\Store\Persistence\SpyStore[]
     */
    protected static $storeCache = [];

    /**
     * @var UnitRepository
     */
    protected $unitRepository;

    /**
     * @var AvailabilityFacadeInterface
     */
    protected $availabilityFacade;

    /**
     * ProductConcreteWriter constructor.
     * @param ProductRepository $productRepository
     * @param DataImportToTouchInterface $touchFacade
     * @param int|null $bulkSize
     * @param UnitRepository $unitRepository
     * @param AvailabilityFacadeInterface $availabilityFacade
     */
    public function __construct(
        ProductRepository $productRepository,
        DataImportToTouchInterface $touchFacade,
        ?int $bulkSize = null,
        UnitRepository $unitRepository,
        AvailabilityFacadeInterface $availabilityFacade
    )
    {
        parent::__construct($productRepository, $touchFacade, $bulkSize);

        $this->unitRepository = $unitRepository;
        $this->availabilityFacade = $availabilityFacade;
    }

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\DepositEntityNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        if (
            in_array(getenv('APPLICATION_ENV'), ['development', 'docker.dev'])
            && !isset($dataSet[static::KEY_ATTRIBUTES][self::ATTRIBUTES_KEY_UNIT])
        ) {
            return;
        }

        $productEntity = $this->importProduct($dataSet);

        $this->productRepository->addProductConcrete($productEntity, $dataSet[static::KEY_ABSTRACT_SKU]);

        $this->importProductLocalizedAttributes($dataSet, $productEntity);
        //$this->importPrices($productEntity);
        $this->importStock($productEntity);
        $this->importBundles($dataSet, $productEntity);
    }

    /**
     * @param DataSetInterface $dataSet
     * @return SpyProduct
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\DepositEntityNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importProduct(DataSetInterface $dataSet)
    {
        $productEntity = SpyProductQuery::create()
            ->filterBySku($dataSet[static::KEY_CONCRETE_SKU])
            ->findOneOrCreate();

        $idAbstract = $this->productRepository->getIdProductAbstractByAbstractSku($dataSet[static::KEY_ABSTRACT_SKU]);

        $attributes = $dataSet[static::KEY_ATTRIBUTES];

        $unitCode = $attributes[self::ATTRIBUTES_KEY_UNIT];

        $idDeposit = $this
            ->unitRepository
            ->getUnitIdByCode($unitCode);

        $attributes[self::ATTRIBUTES_KEY_UNIT] = $this->unitRepository->getUnitNameByCode($unitCode);

        $productEntity
            ->setIsActive(isset($dataSet[static::KEY_IS_ACTIVE]) ? $dataSet[static::KEY_IS_ACTIVE] : true)
            ->setFkProductAbstract($idAbstract)
            ->setFkDeposit($idDeposit)
            ->setAttributes(json_encode($attributes));

        $productEntity->save();

        $this->addMainTouchable(ProductConstants::RESOURCE_TYPE_PRODUCT, $productEntity->getIdProduct());

        return $productEntity;
    }

    /**
     * @param SpyProduct $productEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importStock(SpyProduct $productEntity)
    {
        $stockEntity = SpyStockQuery::create()
            ->filterByName(self::DEFAULT_STOCK)
            ->findOneOrCreate();

        $stockEntity->save();

        $this->addSubTouchable(StockConfig::TOUCH_STOCK_TYPE, $stockEntity->getIdStock());

        $stockProductEntity = SpyStockProductQuery::create()
            ->filterByFkProduct($productEntity->getIdProduct())
            ->filterByFkStock($stockEntity->getIdStock())
            ->findOneOrCreate();

        $stockProductEntity
            ->setQuantity(0)
            ->setIsNeverOutOfStock(true);

        $stockProductEntity->save();

        $this->addMainTouchable(StockConfig::TOUCH_STOCK_PRODUCT, $stockProductEntity->getIdStockProduct());

        $this->availabilityFacade->updateAvailability($productEntity->getSku());
    }

    /**
     * @param SpyProduct $productEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importPrices(SpyProduct $productEntity)
    {
        $priceTypeEntity = SpyPriceTypeQuery::create()
            ->filterByName(self::DEFAULT_PRICE_TYPE)
            ->findOneOrCreate();

        if ($priceTypeEntity->isNew() || $priceTypeEntity->isModified()) {
            $priceTypeEntity->setPriceModeConfiguration(SpyPriceTypeTableMap::COL_PRICE_MODE_CONFIGURATION_GROSS_MODE);
            $priceTypeEntity->save();
        }

        $query = SpyPriceProductQuery::create();
        $query->filterByFkPriceType($priceTypeEntity->getIdPriceType());
        $idProduct = $productEntity->getIdProduct();
        $query->filterByFkProduct($idProduct);

        $productPriceEntity = $query->findOneOrCreate();
        $productPriceEntity->save();

        $storeEntity = $this->getStore(self::DEFAULT_STORE);
        $currencyEntity = $this->getCurrency(self::DEFAULT_CURRENCY);

        $priceProductStoreEntity = SpyPriceProductStoreQuery::create()
            ->filterByFkStore($storeEntity->getPrimaryKey())
            ->filterByFkCurrency($currencyEntity->getPrimaryKey())
            ->filterByFkPriceProduct($productPriceEntity->getPrimaryKey())
            ->findOneOrCreate();

        $priceProductStoreEntity->setGrossPrice(self::DEFAULT_PRICE);

        $priceProductStoreEntity->save();
    }

    /**
     * @param $currencyIsoCode
     * @return \Orm\Zed\Currency\Persistence\SpyCurrency
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getCurrency($currencyIsoCode)
    {
        if (isset(static::$currencyCache[$currencyIsoCode])) {
            return static::$currencyCache[$currencyIsoCode];
        }

        $currencyEntity = SpyCurrencyQuery::create()
            ->filterByCode($currencyIsoCode)
            ->findOne();

        static::$currencyCache[$currencyIsoCode] = $currencyEntity;

        return $currencyEntity;
    }

    /**
     * @param $storeName
     * @return \Orm\Zed\Store\Persistence\SpyStore
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getStore($storeName)
    {
        if (isset(static::$storeCache[$storeName])) {
            return static::$storeCache[$storeName];
        }

        $storeEntity = SpyStoreQuery::create()
            ->filterByName($storeName)
            ->findOne();

        static::$storeCache[$storeName] = $storeEntity;

        return $storeEntity;
    }
}
