<?php
/**
 * Durst - project - VariantTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 20.11.18
 * Time: 13:57
 */

namespace Pyz\Zed\ProductManagement\Communication\Table;

use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductLocalizedAttributesTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use \Spryker\Zed\ProductManagement\Communication\Table\VariantTable as SprykerVariantTable;


class VariantTable extends SprykerVariantTable
{
    /**
     * @param TableConfiguration $config
     * @return array|mixed
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->productQueryQueryContainer
            ->queryProduct()
            ->innerJoinSpyProductAbstract()
            ->useSpyProductLocalizedAttributesQuery(null, Criteria::LEFT_JOIN)
                ->filterByFkLocale($this->localeTransfer->getIdLocale())
                ->_or()
                ->filterByFkLocale(null, Criteria::ISNULL)
            ->endUse()
            ->filterByFkProductAbstract($this->idProductAbstract)
            ->withColumn(SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT, static::COL_ID_PRODUCT_ABSTRACT)
            ->withColumn(SpyProductLocalizedAttributesTableMap::COL_NAME, static::COL_NAME);

        $queryResults = $this->runQuery($query, $config, true);

        $productAbstractCollection = [];
        foreach ($queryResults as $productEntity) {
            $productAbstractCollection[] = $this->generateItem($productEntity);
        }

        return $productAbstractCollection;
    }
}
