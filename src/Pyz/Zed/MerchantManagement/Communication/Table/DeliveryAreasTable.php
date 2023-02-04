<?php

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\MerchantManagement\MerchantManagementConfig;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;


class DeliveryAreasTable extends AbstractTable
{
    const COL_NAME = 'Name';
    const COL_CITY = 'Stadt';
    const COL_ZIP = 'PLZ';
    const COL_ACTION = 'Aktion';

    const TITLE_EDIT_BUTTON = 'Ändern';
    const TITLE_DELETE_BUTTON = 'Löschen';

    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * DeliveryAreasTable constructor.
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     */
    public function __construct(DeliveryAreaFacadeInterface $deliveryAreaFacade)
    {
        $this->deliveryAreaFacade = $deliveryAreaFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyDeliveryAreaTableMap::COL_NAME => self::COL_NAME,
            SpyDeliveryAreaTableMap::COL_CITY => self::COL_CITY,
            SpyDeliveryAreaTableMap::COL_ZIP_CODE => self::COL_ZIP,
            self::COL_ACTION => self::COL_ACTION,
        ]);

        $config->setRawColumns([self::COL_ACTION]);

        $config->setSortable([
            SpyDeliveryAreaTableMap::COL_NAME,
            SpyDeliveryAreaTableMap::COL_CITY,
            SpyDeliveryAreaTableMap::COL_ZIP_CODE,
        ]);

        $config->setSearchable([
            SpyDeliveryAreaTableMap::COL_NAME,
            SpyDeliveryAreaTableMap::COL_CITY,
            SpyDeliveryAreaTableMap::COL_ZIP_CODE,
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
        $deliveryAreaTransfers = $this
            ->deliveryAreaFacade
            ->getDeliveryAreas();

        $results = [];
        foreach($deliveryAreaTransfers as $deliveryAreaTransfer){
            $results[] = [
                SpyDeliveryAreaTableMap::COL_NAME => $deliveryAreaTransfer->getName(),
                SpyDeliveryAreaTableMap::COL_CITY => $deliveryAreaTransfer->getCity(),
                SpyDeliveryAreaTableMap::COL_ZIP_CODE => $deliveryAreaTransfer->getZip(),
                self::COL_ACTION => implode(' ', $this->createActionButtons($deliveryAreaTransfer)),
            ];
        }

        return $results;
    }

    /**
     * @param DeliveryAreaTransfer $deliveryAreaTransfer
     * @return array
     */
    public function createActionButtons(DeliveryAreaTransfer $deliveryAreaTransfer)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(MerchantManagementConfig::UPDATE_DELIVERY_AREA_URL, [
                MerchantManagementConfig::PARAM_ID_DELIVERY_AREA => $deliveryAreaTransfer->getIdDeliveryArea(),
            ]),
            self::TITLE_EDIT_BUTTON
        );

        $urls[] = $this->generateRemoveButton(MerchantManagementConfig::DELETE_DELIVERY_AREA_URL, self::TITLE_DELETE_BUTTON, [
            MerchantManagementConfig::PARAM_ID_DELIVERY_AREA => $deliveryAreaTransfer->getIdDeliveryArea(),
        ]);

        return $urls;
    }

}
