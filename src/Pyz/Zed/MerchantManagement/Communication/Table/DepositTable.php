<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 25.10.17
 * Time: 11:51
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Deposit\Persistence\Map\SpyDepositTableMap;
use Orm\Zed\Deposit\Persistence\SpyDeposit;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class DepositTable extends AbstractTable
{
    public const HEADER_CODE = 'Code';
    public const HEADER_NAME = 'Name';
    public const HEADER_DEPOSIT = 'Pfandwert';
    public const HEADER_DISPLAY_NAME = 'Anzeigename';


    /**
     * @var DepositQueryContainerInterface
     */
    protected $depositQueryContainer;

    /**
     * @var MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * DepositTable constructor.
     * @param DepositQueryContainerInterface $depositQueryContainer
     * @param MoneyFacadeInterface $moneyFacade
     */
    public function __construct(DepositQueryContainerInterface $depositQueryContainer, MoneyFacadeInterface $moneyFacade)
    {
        $this->depositQueryContainer = $depositQueryContainer;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyDepositTableMap::COL_CODE => static::HEADER_CODE,
            SpyDepositTableMap::COL_NAME => static::HEADER_NAME,
            SpyDepositTableMap::COL_DEPOSIT => static::HEADER_DEPOSIT,
            SpyDepositTableMap::COL_PRESENTATION_NAME => static::HEADER_DISPLAY_NAME,
        ]);

        $config->setSortable([
            SpyDepositTableMap::COL_CODE,
            SpyDepositTableMap::COL_NAME,
            SpyDepositTableMap::COL_DEPOSIT,
            SpyDepositTableMap::COL_PRESENTATION_NAME,
        ]);

        $config->setSearchable([
            SpyDepositTableMap::COL_CODE,
            SpyDepositTableMap::COL_NAME,
            SpyDepositTableMap::COL_PRESENTATION_NAME,
        ]);

        $config->setUrl('deposit-table');

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->depositQueryContainer->queryDeposit();
        $queryResults = $this->runQuery($query, $config, true);

        return $this->getResults($queryResults);
    }

    /**
     * @param array|SpyDeposit[] $queryResults
     * @return array
     */
    protected function getResults($queryResults) : array
    {
        $results = [];
        foreach ($queryResults as $depositEntity) {
            $results[] = [
                SpyDepositTableMap::COL_CODE => $depositEntity->getCode(),
                SpyDepositTableMap::COL_NAME => $depositEntity->getName(),
                SpyDepositTableMap::COL_DEPOSIT => $this->formatPrice($depositEntity->getDeposit()),
                SpyDepositTableMap::COL_PRESENTATION_NAME => $depositEntity->getPresentationName(),
            ];
        }

        return $results;
    }

    /**
     * @param int $price
     * @return string
     */
    protected function formatPrice(int $price) : string
    {
        if($price === null)
            return '';
        $moneyTransfer = $this->moneyFacade->fromInteger($price);
        return $this->moneyFacade->formatWithSymbol($moneyTransfer);
    }
}