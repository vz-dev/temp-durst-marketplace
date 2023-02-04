<?php
/**
 * Durst - project - BillingItemTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-26
 * Time: 15:00
 */

namespace Pyz\Zed\Billing\Communication\Table;


use Orm\Zed\Billing\Persistence\Map\DstBillingItemTableMap;
use Orm\Zed\Billing\Persistence\Map\DstBillingPeriodTableMap;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class BillingItemTable extends AbstractTable
{
    public const HEADER_BILLING_ITEM_ID = 'Id';
    public const HEADER_BILLING_PERIOD_ID = 'Billing-Period Id';
    public const HEADER_BILLING_PERIOD_REF = 'Billing Period Ref';
    public const HEADER_SALES_ORDER_ID = 'Id Sales Order';
    public const HEADER_AMOUNT = 'Summe';
    public const HEADER_DISCOUNT_AMOUNT = 'Rabatt-Summe';
    public const HEADER_VOUCHER_DISCOUNT_AMOUNT = 'Gutschein-Rabatt-Summe';
    public const HEADER_DEPOSIT_REFUND_AMOUNT = 'Leergut-Rückgabe Summe';
    public const HEADER_TAX_AMOUNT = 'Mwst.';
    public const HEADER_BRANCH_NAME = 'Branch Name';

    public const KEY_BRANCH_NAME = 'branch_name';
    public const KEY_BILLING_PERIOD_REF = 'billing_period_ref';


    /**
     * @var BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * BillingItemTable constructor.
     * @param BillingQueryContainerInterface $queryContainer
     */
    public function __construct(
        BillingQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstBillingItemTableMap::COL_ID_BILLING_ITEM => static::HEADER_BILLING_ITEM_ID,
            DstBillingItemTableMap::COL_FK_BILLING_PERIOD => static::HEADER_BILLING_PERIOD_ID,
            static::KEY_BILLING_PERIOD_REF => static::HEADER_BILLING_PERIOD_REF,
            static::KEY_BRANCH_NAME => static::HEADER_BRANCH_NAME,
            DstBillingItemTableMap::COL_FK_SALES_ORDER => static::HEADER_SALES_ORDER_ID,
            DstBillingItemTableMap::COL_AMOUNT => static::HEADER_AMOUNT,
            DstBillingItemTableMap::COL_TAX_AMOUNT => static::HEADER_TAX_AMOUNT,
            DstBillingItemTableMap::COL_DISCOUNT_AMOUNT => static::HEADER_DISCOUNT_AMOUNT,
            DstBillingItemTableMap::COL_VOUCHER_DISCOUNT_AMOUNT => static::HEADER_VOUCHER_DISCOUNT_AMOUNT,
            DstBillingItemTableMap::COL_RETURN_DEPOSIT_AMOUNT => static::HEADER_DEPOSIT_REFUND_AMOUNT,
        ]);

        $config->setSortable([

        ]);

        $config->setSearchable([
            DstBillingItemTableMap::COL_ID_BILLING_ITEM => static::HEADER_BILLING_ITEM_ID,
            DstBillingItemTableMap::COL_FK_BILLING_PERIOD => static::HEADER_BILLING_PERIOD_ID,
            DstBillingItemTableMap::COL_FK_SALES_ORDER => static::HEADER_SALES_ORDER_ID,
            static::KEY_BILLING_PERIOD_REF => static::HEADER_BILLING_PERIOD_REF,
            static::KEY_BRANCH_NAME => static::HEADER_BRANCH_NAME,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryBillingItem()
            ->joinWithDstBillingPeriod()
            ->useDstBillingPeriodQuery()
                ->joinWithSpyBranch()
            ->endUse()
            ->addAsColumn(static::KEY_BRANCH_NAME, 'spy_branch.name')
            ->addAsColumn(static::KEY_BILLING_PERIOD_REF, DstBillingPeriodTableMap::COL_BILLING_REFERENCE);

        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                DstBillingItemTableMap::COL_ID_BILLING_ITEM => $item[DstBillingItemTableMap::COL_ID_BILLING_ITEM],
                DstBillingItemTableMap::COL_FK_BILLING_PERIOD => $item[DstBillingItemTableMap::COL_FK_BILLING_PERIOD],
                static::KEY_BILLING_PERIOD_REF => $item[static::KEY_BILLING_PERIOD_REF],
                static::KEY_BRANCH_NAME => $item[static::KEY_BRANCH_NAME],
                DstBillingItemTableMap::COL_FK_SALES_ORDER => $item[DstBillingItemTableMap::COL_FK_SALES_ORDER],
                DstBillingItemTableMap::COL_AMOUNT => $item[DstBillingItemTableMap::COL_AMOUNT],
                DstBillingItemTableMap::COL_DISCOUNT_AMOUNT => $item[DstBillingItemTableMap::COL_DISCOUNT_AMOUNT],
                DstBillingItemTableMap::COL_VOUCHER_DISCOUNT_AMOUNT => $item[DstBillingItemTableMap::COL_VOUCHER_DISCOUNT_AMOUNT],
                DstBillingItemTableMap::COL_RETURN_DEPOSIT_AMOUNT => $item[DstBillingItemTableMap::COL_RETURN_DEPOSIT_AMOUNT],
                DstBillingItemTableMap::COL_TAX_AMOUNT => $item[DstBillingItemTableMap::COL_TAX_AMOUNT],
            ];
        }

        return $results;
    }
}
