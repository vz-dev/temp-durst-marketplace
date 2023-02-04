<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */


namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Writer;

use Orm\Zed\Locale\Persistence\SpyLocaleQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractStoreQuery;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Orm\Zed\Store\Persistence\SpyStoreQuery;
use Orm\Zed\Url\Persistence\SpyUrlQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Repository\ManufacturerRepository;
use Pyz\Zed\DataImport\Business\Model\Product\ProductLocalizedAttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository;
use Pyz\Zed\DataImport\Business\Model\ProductAbstract\ProductAbstractWriterStep as DataImportProductAbstractWriterStep;
use Spryker\Zed\DataImport\Business\Exception\DataKeyNotFoundInDataSetException;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\DataImport\Dependency\Facade\DataImportToTouchInterface;

class ProductAbstractWriterStep extends DataImportProductAbstractWriterStep
{
    const KEY_STORES = 'stores';
    const KEY_ATTRIBUTES_MANUFACTURER = 'manufacturer';

    /**
     * @var int[] Keys are store names, values are store ids.
     */
    protected static $idStoreBuffer;

    /**
     * @var int[] Keys are locale ids, values are locale names.
     */
    protected static $idLocaleBuffer;

    /**
     * @var ManufacturerRepository
     */
    protected $manufacturerRepository;

    public function __construct(
        ProductRepository $productRepository,
        DataImportToTouchInterface $touchFacade,
        ?int $bulkSize = null,
        ManufacturerRepository $manufacturerRepository
    )
    {
        parent::__construct($productRepository, $touchFacade, $bulkSize);

        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\DataImport\Business\Exception\DataKeyNotFoundInDataSetException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\ManufacturerEntityNotFoundException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $productAbstractEntity = $this->importProductAbstract($dataSet);

        $this->productRepository->addProductAbstract($productAbstractEntity);

        $this->importProductAbstractLocalizedAttributes($dataSet, $productAbstractEntity);
        $this->importProductCategories($dataSet, $productAbstractEntity);
        $this->importProductAbstractStores($dataSet, $productAbstractEntity);
        $this->importProductUrls($dataSet, $productAbstractEntity);
    }

    /**
     * @param DataSetInterface $dataSet
     * @return SpyProductAbstract
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\AkeneoPimMiddlewareConnector\Exception\ManufacturerEntityNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importProductAbstract(DataSetInterface $dataSet)
    {
        $productAbstractEntity = SpyProductAbstractQuery::create()
            ->filterBySku($dataSet[static::KEY_ABSTRACT_SKU])
            ->findOneOrCreate();

        $productAbstractEntity
            ->setIsFeatured(false)
            ->setColorCode($dataSet[static::KEY_COLOR_CODE])
            ->setFkTaxSet($dataSet[static::KEY_ID_TAX_SET])
            ->setAttributes(json_encode($dataSet[static::KEY_ATTRIBUTES]))
            ->setNewFrom($dataSet[static::KEY_NEW_FROM])
            ->setNewTo($dataSet[static::KEY_NEW_TO]);


        if(
            isset($dataSet[static::KEY_ATTRIBUTES]) &&
            isset($dataSet[static::KEY_ATTRIBUTES][static::KEY_ATTRIBUTES_MANUFACTURER])
        ){
            $productAbstractEntity
                ->setFkManufacturer(
                    $this->manufacturerRepository->getManufacturerIdByCode(
                        $dataSet[static::KEY_ATTRIBUTES][static::KEY_ATTRIBUTES_MANUFACTURER]
                    )
                );
        }

        if ($productAbstractEntity->isNew() || $productAbstractEntity->isModified()) {
            $productAbstractEntity->save();
        }

        return $productAbstractEntity;
    }

    /**
     * @param DataSetInterface $dataSet
     * @param SpyProductAbstract $productAbstractEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importProductAbstractStores(DataSetInterface $dataSet, SpyProductAbstract $productAbstractEntity)
    {
        foreach ($dataSet[static::KEY_STORES] as $storeName) {
            (new SpyProductAbstractStoreQuery())
                ->filterByFkProductAbstract($productAbstractEntity->getIdProductAbstract())
                ->filterByFkStore($this->getIdStoreByName($storeName))
                ->findOneOrCreate()
                ->save();
        }
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\DataKeyNotFoundInDataSetException
     *
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importProductCategories(DataSetInterface $dataSet, SpyProductAbstract $productAbstractEntity)
    {
        $categoryKeys = $this->getCategoryKeys($dataSet[static::KEY_CATEGORY_KEY]);
        $categoryProductOrder = $this->getCategoryProductOrder($dataSet[static::KEY_CATEGORY_PRODUCT_ORDER]);

        $categoryIds = [];
        foreach ($categoryKeys as $index => $categoryKey) {
            if($categoryKey === ''){
                continue;
            }
            if (!isset($dataSet[static::KEY_CATEGORY_KEYS][$categoryKey])) {
                throw new DataKeyNotFoundInDataSetException(sprintf(
                    'The category with key "%s" was not found in categoryKeys. Maybe there is a typo. Given Categories: "%s"',
                    $categoryKey,
                    implode(array_values($dataSet[static::KEY_CATEGORY_KEYS]))
                ));
            }
            $productOrder = null;
            if (count($categoryProductOrder) > 0 && isset($categoryProductOrder[$index])) {
                $productOrder = $categoryProductOrder[$index];
            }

            $productCategoryEntity = SpyProductCategoryQuery::create()
                ->filterByFkProductAbstract($productAbstractEntity->getIdProductAbstract())
                ->filterByFkCategory($dataSet[static::KEY_CATEGORY_KEYS][$categoryKey])
                ->findOneOrCreate();

            $categoryIds[] = $dataSet[static::KEY_CATEGORY_KEYS][$categoryKey];
            $productCategoryEntity
                ->setProductOrder($productOrder);

            if ($productCategoryEntity->isNew() || $productCategoryEntity->isModified()) {
                $productCategoryEntity->save();
            }
        }

        $this->removeOldProductCategories($categoryIds, $productAbstractEntity->getIdProductAbstract());
    }

    /**
     * @param array $categoryIds
     * @param int $idProductAbstract
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function removeOldProductCategories(array $categoryIds, int $idProductAbstract)
    {
        $productCategoryEntities = SpyProductCategoryQuery::create()
            ->filterByFkProductAbstract($idProductAbstract)
            ->filterByFkCategory($categoryIds, Criteria::NOT_IN)
            ->find();

        foreach ($productCategoryEntities as $productCategoryEntity) {
            $productCategoryEntity->delete();
        }
    }

    /**
     * @param string $storeName
     *
     * @return int
     */
    protected function getIdStoreByName($storeName)
    {
        if (!isset(static::$idStoreBuffer[$storeName])) {
            static::$idStoreBuffer[$storeName] =
                SpyStoreQuery::create()->findOneByName($storeName)->getIdStore();
        }

        return static::$idStoreBuffer[$storeName];
    }

    /**
     * @param DataSetInterface $dataSet
     * @param SpyProductAbstract $productAbstractEntity
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function importProductUrls(DataSetInterface $dataSet, SpyProductAbstract $productAbstractEntity)
    {
        foreach ($dataSet[ProductLocalizedAttributesExtractorStep::KEY_LOCALIZED_ATTRIBUTES] as $idLocale => $localizedAttributes) {
            $abstractProductUrl = $this->generateUrlSlug($localizedAttributes[static::KEY_NAME]);
            $abstractProductUrl = '/' . $this->getLocaleNameById($idLocale) . '/' . $abstractProductUrl . '-' .$productAbstractEntity->getIdProductAbstract();
            $this->cleanupRedirectUrls($abstractProductUrl);

            $urlEntity = SpyUrlQuery::create()
                ->filterByFkLocale($idLocale)
                ->filterByFkResourceProductAbstract($productAbstractEntity->getIdProductAbstract())
                ->findOneOrCreate();

            $urlEntity->setUrl($abstractProductUrl);

            if ($urlEntity->isNew() || $urlEntity->isModified()) {
                $urlEntity->save();
            }
        }
    }


    /**
     * @param string $value
     * @return string
     */
    protected function generateUrlSlug($value)
    {
        if (function_exists('iconv')) {
            $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        }

        $value = preg_replace("/[^a-zA-Z0-9 -]/", "", trim($value));
        $value = mb_strtolower($value);
        $value = str_replace(' ', '-', $value);
        $value = preg_replace('/(\-)\1+/', '$1', $value);

        return $value;
    }

    /**
     * @param int $localeId
     *
     * @return int
     */
    protected function getLocaleNameById($localeId)
    {
        if (!isset(static::$idLocaleBuffer[$localeId])) {
            static::$idLocaleBuffer[$localeId] =
                mb_strtolower(SpyLocaleQuery::create()->findOneByIdLocale($localeId)->getLocaleName());
        }

        return static::$idLocaleBuffer[$localeId];
    }

}