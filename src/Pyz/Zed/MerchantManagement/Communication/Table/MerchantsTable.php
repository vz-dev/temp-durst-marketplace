<?php

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Orm\Zed\Sales\Persistence\Map\DstSoftwarePackageTableMap;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class MerchantsTable extends AbstractTable
{

    const ACTION = 'Action';
    const UPDATE_MERCHANT_URL = '/merchant-management/merchant/update';
    const DEACTIVATE_MERCHANT_URL = '/merchant-management/merchant/deactivate-merchant';
    const ACTIVATE_MERCHANT_URL = '/merchant-management/merchant/activate-merchant';
    const DELETE_MERCHANT_URL = '/merchant-management/merchant/delete';
    const PARAM_ID_MERCHANT = 'id-merchant';

    public const COL_COUNT_MERCHANT_USERS = 'merchantUsers';

    public const LABEL_COUNT_MERCHANT_USERS = 'Anzahl Nutzer';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     * @param \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface $utilDateTimeService
     */
    public function __construct(MerchantQueryContainerInterface $merchantQueryContainer, UtilDateTimeServiceInterface $utilDateTimeService)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->utilDateTimeService = $utilDateTimeService;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyMerchantTableMap::COL_MERCHANTNAME => 'E-mail',
            DstSoftwarePackageTableMap::COL_NAME => 'Software-Paket',
            SpyMerchantTableMap::COL_FIRST_NAME => 'First Name',
            SpyMerchantTableMap::COL_LAST_NAME => 'Last Name',
            static::COL_COUNT_MERCHANT_USERS => static::LABEL_COUNT_MERCHANT_USERS,
            SpyMerchantTableMap::COL_LAST_LOGIN => 'Last Login',
            SpyMerchantTableMap::COL_STATUS => 'Status',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([SpyMerchantTableMap::COL_STATUS, self::ACTION]);

        $config->setSortable([
            SpyMerchantTableMap::COL_MERCHANTNAME,
            DstSoftwarePackageTableMap::COL_NAME,
            SpyMerchantTableMap::COL_FIRST_NAME,
            SpyMerchantTableMap::COL_LAST_NAME,
            SpyMerchantTableMap::COL_STATUS,
            SpyMerchantTableMap::COL_LAST_LOGIN,
        ]);

        $config->setSearchable([
            SpyMerchantTableMap::COL_MERCHANTNAME,
            SpyMerchantTableMap::COL_FIRST_NAME,
            SpyMerchantTableMap::COL_LAST_NAME,
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
        $merchantQuery = $this
            ->merchantQueryContainer
            ->queryMerchant()
            ->joinDstMerchantUser()
            ->joinWithDstSoftwarePackage()
            ->groupByIdMerchant();

        $queryResults = $this->runQuery($merchantQuery, $config, true);

        $results = [];
        /** @var \Orm\Zed\Merchant\Persistence\SpyMerchant $merchantEntity */
        foreach ($queryResults as $merchantEntity) {
            $results[] = [
                SpyMerchantTableMap::COL_MERCHANTNAME => $merchantEntity->getMerchantname(),
                DstSoftwarePackageTableMap::COL_NAME => $merchantEntity->getDstSoftwarePackage()->getName(),
                SpyMerchantTableMap::COL_FIRST_NAME => $merchantEntity->getFirstName(),
                SpyMerchantTableMap::COL_LAST_NAME => $merchantEntity->getLastName(),
                static::COL_COUNT_MERCHANT_USERS => $merchantEntity->getDstMerchantUsers()->count(),
                SpyMerchantTableMap::COL_LAST_LOGIN => $this->utilDateTimeService->formatDateTime($merchantEntity->getLastLogin()),
                SpyMerchantTableMap::COL_STATUS => $this->createStatusLabel($merchantEntity),
                self::ACTION => implode(' ', $this->createActionButtons($merchantEntity)),
            ];
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchant
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createActionButtons(SpyMerchant $merchant)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_MERCHANT_URL, [
                self::PARAM_ID_MERCHANT => $merchant->getIdMerchant(),
            ]),
            'Edit'
        );

        $urls[] = $this->createStatusButton($merchant);

        $urls[] = $this->generateRemoveButton(self::DELETE_MERCHANT_URL, 'Delete', [
            self::PARAM_ID_MERCHANT => $merchant->getIdMerchant(),
        ]);

        return $urls;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchant
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createStatusLabel(SpyMerchant $merchant)
    {
        $statusLabel = '';
        switch ($merchant->getStatus()) {
            case SpyMerchantTableMap::COL_STATUS_ACTIVE:
                $statusLabel = '<span class="label label-success" title="Active">Active</span>';
                break;
            case SpyMerchantTableMap::COL_STATUS_BLOCKED:
                $statusLabel = '<span class="label label-danger" title="Deactivated">Deactivated</span>';
                break;
            case SpyMerchantTableMap::COL_STATUS_DELETED:
                $statusLabel = '<span class="label label-default" title="Deleted">Deleted</span>';
                break;
        }

        return $statusLabel;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $merchant
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createStatusButton(SpyMerchant $merchant)
    {
        if ($merchant->getStatus() === SpyMerchantTableMap::COL_STATUS_BLOCKED) {
            return $this->generateViewButton(
                Url::generate(self::ACTIVATE_MERCHANT_URL, [
                    self::PARAM_ID_MERCHANT => $merchant->getIdMerchant(),
                ]),
                'Activate'
            );
        }

        return $urls[] = $this->generateViewButton(
            Url::generate(self::DEACTIVATE_MERCHANT_URL, [
                self::PARAM_ID_MERCHANT => $merchant->getIdMerchant(),
            ]),
            'Deactivate'
        );
    }

}
