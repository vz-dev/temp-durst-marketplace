<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 04.01.18
 * Time: 09:52
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Merchant\Persistence\Map\SpyPaymentMethodTableMap;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class PaymentMethodsTable extends AbstractTable
{
    const ACTION = 'Action';

    const UPDATE_PAYMENT_METHOD_URL = '/merchant-management/payment-method/edit';
    const DELETE_PAYMENT_METHOD_URL = '/merchant-management/payment-method/delete';

    const PARAM_ID_PAYMENT_METHOD = 'id-payment-method';

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
            SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD => 'ID',
            SpyPaymentMethodTableMap::COL_NAME => 'Name',
            SpyPaymentMethodTableMap::COL_CODE => 'Code',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([self::ACTION]);

        $config->setSortable([
            SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD,
            SpyPaymentMethodTableMap::COL_NAME,
        ]);

        $config->setSearchable([
            SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD,
            SpyPaymentMethodTableMap::COL_NAME,
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
        $query = $this->merchantQueryContainer->queryPaymentMethod();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD => $item[SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD],
                SpyPaymentMethodTableMap::COL_NAME => $item[SpyPaymentMethodTableMap::COL_NAME],
                SpyPaymentMethodTableMap::COL_CODE => $item[SpyPaymentMethodTableMap::COL_CODE],
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
            Url::generate(self::UPDATE_PAYMENT_METHOD_URL, [
                self::PARAM_ID_PAYMENT_METHOD => $merchant[SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD],
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(self::DELETE_PAYMENT_METHOD_URL, 'Delete', [
            self::PARAM_ID_PAYMENT_METHOD => $merchant[SpyPaymentMethodTableMap::COL_ID_PAYMENT_METHOD],
        ]);

        return $urls;
    }
}
