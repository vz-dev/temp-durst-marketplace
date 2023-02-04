<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.01.18
 * Time: 14:04
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Merchant\Persistence\Map\SpyEnumSalutationTableMap;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SalutationTable extends AbstractTable
{
    const ACTION = 'Action';

    const UPDATE_SALUTATION_URL = '/merchant-management/salutation/edit';
    const DELETE_SALUTATION_URL = '/merchant-management/salutation/delete';

    const PARAM_ID_SALUTATION = 'id-salutation';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * PaymentMethodsTable constructor.
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     */
    public function __construct(MerchantQueryContainerInterface $merchantQueryContainer)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
    }


    /**
     * @param TableConfiguration $config
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION => 'ID',
            SpyEnumSalutationTableMap::COL_NAME => 'Name',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([self::ACTION]);

        $config->setSortable([
            SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION,
            SpyEnumSalutationTableMap::COL_NAME,
        ]);

        $config->setSearchable([
            SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION,
            SpyEnumSalutationTableMap::COL_NAME,
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
        $query = $this->merchantQueryContainer->queryEnumSalutation();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION => $item[SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION],
                SpyEnumSalutationTableMap::COL_NAME => $item[SpyEnumSalutationTableMap::COL_NAME],
                self::ACTION => implode(' ', $this->createActionButtons($item)),
            ];
        }

        return $results;
    }

    /**
     * @param array $salutation
     *
     * @return array
     */
    public function createActionButtons(array $salutation)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_SALUTATION_URL, [
                self::PARAM_ID_SALUTATION => $salutation[SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION],
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(self::DELETE_SALUTATION_URL, 'Delete', [
            self::PARAM_ID_SALUTATION => $salutation[SpyEnumSalutationTableMap::COL_ID_ENUM_SALUTATION],
        ]);

        return $urls;
    }
}
