<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.01.18
 * Time: 14:21
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;


use Orm\Zed\TermsOfService\Persistence\Map\SpyTermsOfServiceTableMap;
use Pyz\Zed\TermsOfService\Persistence\TermsOfServiceQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class TermsOfServiceTable extends AbstractTable
{
    const ACTION = 'Action';

    const UPDATE_TERMS_OF_SERVICE_URL = '/merchant-management/terms-of-service/edit';
    const DELETE_TERMS_OF_SERVICE_URL = '/merchant-management/terms-of-service/delete';

    const PARAM_ID_TERMS_OF_SERVICE = 'id-terms-of-service';

    /**
     * @var TermsOfServiceQueryContainerInterface
     */
    protected $termsOfServiceQueryContainer;

    /**
     * TermsOfServiceTable constructor.
     * @param TermsOfServiceQueryContainerInterface $termsOfServiceQueryContainer
     */
    public function __construct(TermsOfServiceQueryContainerInterface $termsOfServiceQueryContainer)
    {
        $this->termsOfServiceQueryContainer = $termsOfServiceQueryContainer;
    }


    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE => 'ID',
            SpyTermsOfServiceTableMap::COL_NAME => 'Bezeichnung',
            SpyTermsOfServiceTableMap::COL_HINT_TEXT => 'Hinweis',
            SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL => 'Aktiv bis',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([self::ACTION]);

        $config->setSortable([
            SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE,
            SpyTermsOfServiceTableMap::COL_NAME,
            SpyTermsOfServiceTableMap::COL_HINT_TEXT,
        ]);

        $config->setSearchable([
            SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE,
            SpyTermsOfServiceTableMap::COL_NAME,
            SpyTermsOfServiceTableMap::COL_HINT_TEXT,
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
        $query = $this->termsOfServiceQueryContainer->queryTermsOfService();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE => $item[SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE],
                SpyTermsOfServiceTableMap::COL_NAME => $item[SpyTermsOfServiceTableMap::COL_NAME],
                SpyTermsOfServiceTableMap::COL_HINT_TEXT => $item[SpyTermsOfServiceTableMap::COL_HINT_TEXT],
                SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL => $item[SpyTermsOfServiceTableMap::COL_ACTIVE_UNTIL],
                self::ACTION => implode(' ', $this->createActionButtons($item)),
            ];
        }

        return $results;
    }

    /**
     * @param array $merchant
     *
     * @return array
     */
    public function createActionButtons(array $merchant)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_TERMS_OF_SERVICE_URL, [
                self::PARAM_ID_TERMS_OF_SERVICE => $merchant[SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE],
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(self::DELETE_TERMS_OF_SERVICE_URL, 'Delete', [
            self::PARAM_ID_TERMS_OF_SERVICE => $merchant[SpyTermsOfServiceTableMap::COL_ID_TERMS_OF_SERVICE],
        ]);

        return $urls;
    }
}
