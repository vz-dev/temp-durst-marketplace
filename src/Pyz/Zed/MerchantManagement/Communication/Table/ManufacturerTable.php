<?php
/**
 * Durst - project - ManufacturerTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.09.18
 * Time: 10:52
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;


use Orm\Zed\Product\Persistence\Map\SpyManufacturerTableMap;
use Orm\Zed\Product\Persistence\SpyManufacturer;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class ManufacturerTable extends AbstractTable
{
    public const HEADER_CODE = 'Code';
    public const HEADER_NAME = 'Name';
    public const HEADER_HOMEPAGE = 'Homepage';

    /**
     * @var ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * ManufacturerTable constructor.
     * @param ProductQueryContainerInterface $productQueryContainer
     */
    public function __construct(ProductQueryContainerInterface $productQueryContainer)
    {
        $this->productQueryContainer = $productQueryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyManufacturerTableMap::COL_CODE => static::HEADER_CODE,
            SpyManufacturerTableMap::COL_NAME => static::HEADER_NAME,
            SpyManufacturerTableMap::COL_HOMEPAGE => static::HEADER_HOMEPAGE,
        ]);

        $config->setSortable([
            SpyManufacturerTableMap::COL_CODE,
            SpyManufacturerTableMap::COL_NAME,
        ]);

        $config->setSearchable([
            SpyManufacturerTableMap::COL_CODE,
            SpyManufacturerTableMap::COL_NAME,
        ]);

        $config->setUrl('manufacturer-table');

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->productQueryContainer->queryManufacturer();
        $queryResults = $this->runQuery($query, $config, true);

        return $this->getResults($queryResults);
    }

    /**
     * @param array|SpyManufacturer[] $queryResults
     * @return array
     */
    protected function getResults($queryResults) : array
    {
        $results = [];
        foreach ($queryResults as $manufacturerEntity) {
            $results[] = [
                SpyManufacturerTableMap::COL_CODE => $manufacturerEntity->getCode(),
                SpyManufacturerTableMap::COL_NAME => $manufacturerEntity->getName(),
                SpyManufacturerTableMap::COL_HOMEPAGE => $manufacturerEntity->getHomepage(),
            ];
        }

        return $results;
    }
}