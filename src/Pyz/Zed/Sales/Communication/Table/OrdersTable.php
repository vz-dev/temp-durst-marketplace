<?php
/**
 * Durst - project - OrdersTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 04.10.18
 * Time: 06:55
 */

namespace Pyz\Zed\Sales\Communication\Table;

use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\Sales\Communication\Table\OrdersTable as SprykerOrdersTable;
use Spryker\Zed\Sales\Communication\Table\OrdersTableQueryBuilder;

class OrdersTable extends SprykerOrdersTable
{
    public const COL_BRANCH_NAME = 'branch_name';

    /**
     * @return array
     */
    protected function getHeaderFields()
    {
        return [
            SpySalesOrderTableMap::COL_ID_SALES_ORDER => '#',
            SpySalesOrderTableMap::COL_ORDER_REFERENCE => 'Order Reference',
            SpySalesOrderTableMap::COL_CREATED_AT => 'Created',
            SpySalesOrderTableMap::COL_CUSTOMER_REFERENCE => 'Customer Full Name',
            SpySalesOrderTableMap::COL_EMAIL => 'Email',
            static::COL_BRANCH_NAME => 'Branch',
            static::ITEM_STATE_NAMES_CSV => 'Order State',
            static::GRAND_TOTAL => 'GrandTotal',
            static::NUMBER_OF_ORDER_ITEMS => 'Number of Items',
            static::URL => 'Actions',
        ];
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->buildQuery();
        $query = $this->addBranchToQuery($query);
        $queryResults = $this->runQuery($query, $config);

        return $this->formatQueryData($queryResults);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderQuery $query
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    protected function addBranchToQuery(SpySalesOrderQuery $query) : SpySalesOrderQuery
    {
        return $query
            ->addJoin(SpySalesOrderTableMap::COL_FK_BRANCH, SpyBranchTableMap::COL_ID_BRANCH)
            ->withColumn(SpyBranchTableMap::COL_NAME, self::COL_BRANCH_NAME);
    }

    /**
     * @param array $queryResults
     *
     * @return array
     */
    protected function formatQueryData(array $queryResults)
    {
        $results = [];
        foreach ($queryResults as $item) {
            $itemLine = [
                SpySalesOrderTableMap::COL_ID_SALES_ORDER => $item[SpySalesOrderTableMap::COL_ID_SALES_ORDER],
                SpySalesOrderTableMap::COL_ORDER_REFERENCE => $item[SpySalesOrderTableMap::COL_ORDER_REFERENCE],
                SpySalesOrderTableMap::COL_CREATED_AT => $this->utilDateTimeService->formatDateTime($item[SpySalesOrderTableMap::COL_CREATED_AT]),
                SpySalesOrderTableMap::COL_CUSTOMER_REFERENCE => $this->formatCustomer($item),
                SpySalesOrderTableMap::COL_EMAIL => $this->formatEmailAddress($item[SpySalesOrderTableMap::COL_EMAIL]),
                static::COL_BRANCH_NAME => $item[self::COL_BRANCH_NAME],
                static::ITEM_STATE_NAMES_CSV => $this->groupItemStateNames($item[OrdersTableQueryBuilder::FIELD_ITEM_STATE_NAMES_CSV]),
                static::GRAND_TOTAL => $this->getGrandTotal($item),
                static::NUMBER_OF_ORDER_ITEMS => $item[OrdersTableQueryBuilder::FIELD_NUMBER_OF_ORDER_ITEMS],
                static::URL => implode(' ', $this->createActionUrls($item)),
            ];
            $itemLine = $this->applyUIPlugins($itemLine);
            $results[] = $itemLine;
        }

        return $results;
    }

    /**
     * @return array
     */
    protected function getSearchableFields()
    {
        return [
            SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            SpySalesOrderTableMap::COL_ORDER_REFERENCE,
            SpySalesOrderTableMap::COL_FIRST_NAME,
            SpySalesOrderTableMap::COL_LAST_NAME,
            SpySalesOrderTableMap::COL_CREATED_AT,
            SpySalesOrderTableMap::COL_EMAIL,
            SpyBranchTableMap::COL_NAME,
        ];
    }

    /**
     * @return array
     */
    protected function getSortableFields()
    {
        return [
            SpySalesOrderTableMap::COL_ID_SALES_ORDER,
            SpySalesOrderTableMap::COL_ORDER_REFERENCE,
            SpySalesOrderTableMap::COL_CREATED_AT,
            SpySalesOrderTableMap::COL_EMAIL,
            static::NUMBER_OF_ORDER_ITEMS,
            static::COL_BRANCH_NAME,
        ];
    }
}
