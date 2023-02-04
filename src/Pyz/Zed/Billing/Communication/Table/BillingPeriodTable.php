<?php
/**
 * Durst - project - BillingPeriodTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 17:44
 */

namespace Pyz\Zed\Billing\Communication\Table;

use Orm\Zed\Billing\Persistence\Map\DstBillingPeriodTableMap;
use Pyz\Zed\Billing\Communication\Controller\BillingPeriodController;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class BillingPeriodTable extends AbstractTable
{
    public const HEADER_BILLING_PERIOD_ID = 'ID';
    public const HEADER_BRANCH_ID = 'Branch-ID';
    public const HEADER_BILLING_REF = 'Invoice-Ref.';
    public const HEADER_START_DATE = 'Start-Date';
    public const HEADER_END_DATE = 'End-Date';
    public const HEADER_ACTION = 'Action';

    protected const TITLE_DETAIL_BUTTON = 'Detail';

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * BillingPeriodTable constructor.
     *
     * @param \Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface $queryContainer
     */
    public function __construct(
        BillingQueryContainerInterface $queryContainer
    ) {
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
            DstBillingPeriodTableMap::COL_ID_BILLING_PERIOD => self::HEADER_BILLING_PERIOD_ID,
            DstBillingPeriodTableMap::COL_FK_BRANCH => self::HEADER_BRANCH_ID,
            DstBillingPeriodTableMap::COL_BILLING_REFERENCE => self::HEADER_BILLING_REF,
            DstBillingPeriodTableMap::COL_START_DATE => self::HEADER_START_DATE,
            DstBillingPeriodTableMap::COL_END_DATE => self::HEADER_END_DATE,
            self::HEADER_ACTION => 'Aktion',
        ]);

        $config
            ->setRawColumns([
                self::HEADER_ACTION
            ]);

        $config->setSortable([
        ]);

        $config->setSearchable([
            DstBillingPeriodTableMap::COL_ID_BILLING_PERIOD => self::HEADER_BILLING_PERIOD_ID,
            DstBillingPeriodTableMap::COL_FK_BRANCH => self::HEADER_BRANCH_ID,
            DstBillingPeriodTableMap::COL_BILLING_REFERENCE => self::HEADER_BILLING_REF,
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
        $query = $this->queryContainer->queryBillingPeriod();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                DstBillingPeriodTableMap::COL_ID_BILLING_PERIOD => $item[DstBillingPeriodTableMap::COL_ID_BILLING_PERIOD],
                DstBillingPeriodTableMap::COL_FK_BRANCH => $item[DstBillingPeriodTableMap::COL_FK_BRANCH],
                DstBillingPeriodTableMap::COL_BILLING_REFERENCE => $item[DstBillingPeriodTableMap::COL_BILLING_REFERENCE],
                DstBillingPeriodTableMap::COL_START_DATE => $item[DstBillingPeriodTableMap::COL_START_DATE],
                DstBillingPeriodTableMap::COL_END_DATE => $item[DstBillingPeriodTableMap::COL_END_DATE],
                self::HEADER_ACTION => implode(' ', $this->createButtons($item)),
            ];
        }

        return $results;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    protected function createButtons(array $item): array
    {
        $urls[] = $this->generateEditButton(
            Url::generate(BillingPeriodController::URL_DETAIL, [
                BillingPeriodController::PARAM_ID_BILLING_PERIOD => $item[DstBillingPeriodTableMap::COL_ID_BILLING_PERIOD],
            ]),
            self::TITLE_DETAIL_BUTTON
        );

        return $urls;
    }
}
