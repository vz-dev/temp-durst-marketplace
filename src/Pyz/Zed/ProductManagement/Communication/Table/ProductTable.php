<?php
/**
 * Durst - project - ProductTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.09.18
 * Time: 21:05
 */

namespace Pyz\Zed\ProductManagement\Communication\Table;

use Orm\Zed\Product\Persistence\Map\SpyProductAbstractLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Tax\Persistence\Map\SpyTaxSetTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductManagement\Communication\Table\ProductTable as SprykerProductTable;

class ProductTable extends SprykerProductTable
{
    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return mixed
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            static::COL_ID_PRODUCT_ABSTRACT => 'Product ID',
            static::COL_NAME => 'Name',
            static::COL_SKU => 'Sku',
            static::COL_TAX_SET => 'Tax Set',
            static::COL_VARIANT_COUNT => 'Variants',
            static::COL_STATUS => 'Status',
            static::COL_IS_BUNDLE => 'Contains bundles',
            static::COL_STORE_RELATION => 'Store',
            static::COL_ACTIONS => 'Actions',
        ]);

        $config->setRawColumns([
            static::COL_STATUS,
            static::COL_IS_BUNDLE,
            static::COL_STORE_RELATION,
            static::COL_ACTIONS,
        ]);

        $config->setSearchable([
            SpyProductAbstractTableMap::COL_SKU,
            SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT,
            SpyProductAbstractTableMap::COL_ATTRIBUTES,
        ]);

        $config->setSortable([
            static::COL_ID_PRODUCT_ABSTRACT,
            static::COL_SKU,
            static::COL_NAME,
            static::COL_TAX_SET,
        ]);

        $config->setDefaultSortDirection(TableConfiguration::SORT_DESC);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array|mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->productQueryQueryContainer
            ->queryProductAbstract()
            ->innerJoinSpyTaxSet()
            ->useSpyProductAbstractLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
                ->filterByFkLocale($this->localeTransfer->getIdLocale())
                ->_or()
                ->filterByFkLocale(null, Criteria::ISNULL)
            ->endUse()
            ->withColumn(SpyProductAbstractLocalizedAttributesTableMap::COL_NAME, static::COL_NAME)
            ->withColumn(SpyTaxSetTableMap::COL_NAME, static::COL_TAX_SET);

        $queryResults = $this->runQuery($query, $config, true);

        $productAbstractCollection = [];
        foreach ($queryResults as $productAbstractEntity) {
            $productAbstractCollection[] = $this->generateItem($productAbstractEntity);
        }

        return $productAbstractCollection;
    }

    /**
     * @param SpyProductAbstract $productAbstractEntity
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function generateItem(SpyProductAbstract $productAbstractEntity) : array
    {
        return [
            static::COL_ID_PRODUCT_ABSTRACT => $productAbstractEntity->getIdProductAbstract(),
            static::COL_SKU => $productAbstractEntity->getSku(),
            static::COL_NAME => $this->getNameFromAttributes($productAbstractEntity->getAttributes()),
            static::COL_TAX_SET => $productAbstractEntity->getVirtualColumn(static::COL_TAX_SET),
            static::COL_VARIANT_COUNT => $productAbstractEntity->getSpyProducts()->count(),
            static::COL_STATUS => $this->getAbstractProductStatusLabel($productAbstractEntity),
            static::COL_IS_BUNDLE => $this->getIsBundleProductLable($productAbstractEntity),
            static::COL_STORE_RELATION => $this->getStoreNames($productAbstractEntity->getIdProductAbstract()),
            static::COL_ACTIONS => implode(' ', $this->createActionColumn($productAbstractEntity)),
        ];
    }


    /**
     * @param string $attributes
     * @return string
     */
    protected function getNameFromAttributes(string $attributes) : string
    {
        $json = json_decode($attributes, true);

        return $json[static::COL_NAME];
    }



}
