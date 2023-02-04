<?php
/**
 * Durst - project - OpenOrdersExport.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.11.20
 * Time: 14:10
 */

namespace Pyz\Zed\Integra\Business\Model\Export;

use Orm\Zed\DeliveryArea\Persistence\Map\SpyConcreteTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderCommentTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderItemTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManager;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class OpenOrdersExport extends AbstractExport
{
    protected const STATE = 'OPN';

    /**
     * OpenOrdersExport constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idBranch
     *
     * @return array
     */
    public function getMappedData(int $idBranch): array
    {
        $this->idBranch = $idBranch;
        $this->affectedOrderIds = [];
        $data = $this->collectData($idBranch);
        return $this->mapData($data);
    }

    /**
     * @return void
     */
    public function updateOrders(): void
    {
        if (count($this->affectedOrderIds) < 1) {
            return;
        }

        parent::executeUpdateQuery(
            $this->queryContainer->updateOrdersExported($this->affectedOrderIds)
        );
    }

    /**
     * @param int $idBranch
     *
     * @return iterable
     */
    protected function collectData(int $idBranch): iterable
    {
        return $this
            ->queryContainer
            ->queryExportableOrdersForBranch($idBranch)
            ->find()
            ->toArray();
    }

    /**
     * @param iterable $data
     *
     * @return array
     */
    protected function mapData(iterable $data): array
    {
        $mappedData = [];
        foreach ($data as $datum) {
            if (in_array($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->affectedOrderIds) !== true) {
                $this->affectedOrderIds[] = $datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER];
            }

            $mappedData[] = [
                static::HEADER_ORDER_NO => $datum[SpySalesOrderTableMap::COL_ORDER_REFERENCE],
                static::HEADER_RECEIPT_DID => $datum[SpySalesOrderTableMap::COL_INTEGRA_RECEIPT_DID],
                static::HEADER_SHIPMENT_TYPE => $this->getVersandartByDateAndZip($datum[SpyConcreteTimeSlotTableMap::COL_START_TIME], $datum[SpyDeliveryAreaTableMap::COL_ZIP_CODE], $datum[SpyTimeSlotTableMap::COL_INTEGRA_TOUR_NO]),
                static::HEADER_TOUR_NR => '',
                static::HEADER_ORDER_DATE => $this->getFormattedDate($datum[SpySalesOrderTableMap::COL_CREATED_AT]),
                static::HEADER_DELIVERY_START => $this->getBerlinEuropeTimeFromUtc($datum[SpyConcreteTimeSlotTableMap::COL_START_TIME]),
                static::HEADER_DELIVERY_END => $this->getBerlinEuropeTimeFromUtc($datum[SpyConcreteTimeSlotTableMap::COL_END_TIME]),
                static::HEADER_COMMENT => $this->stripIllegalChars($datum[SpySalesOrderCommentTableMap::COL_MESSAGE]),
                static::HEADER_SELLER_GLN => static::SELLER_GLN,
                static::HEADER_CUSTOMER_NO => $datum[SpySalesOrderTableMap::COL_INTEGRA_CUSTOMER_NO],
                static::HEADER_POSITION_DID => $datum[SpySalesOrderItemTableMap::COL_INTEGRA_POSITION_DID],
                static::HEADER_POSITION_NO => $this->getPosByOrderIdAndSkuState($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU], ''),
                static::HEADER_SKU => $this->getStringWithNumbersOnly($datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU]),
                static::HEADER_PRODUCT => $datum[SpySalesOrderItemTableMap::COL_NAME],
                static::HEADER_QUANTITY => $datum[IntegraQueryContainerInterface::VIRTUAL_COL_QUANTITY],
                static::HEADER_TYPE => $this->getUnitTypeFromMerchantSku($datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU]),
                static::HEADER_STATE => static::STATE,
                static::HEADER_TRANSACTIONCODE_UNZER => '',
                static::HEADER_RETURN_REASON => '',
                static::HEADER_TOUR_TRIP_NO => $this->getTourTripNoFromDateTime($datum[SpyConcreteTimeSlotTableMap::COL_START_TIME], $datum[SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO]),
                static::HEADER_AMOUNT_PAID => '',
            ];
        }

        $csvData = [];
        $csvData[] = static::HEADER;
        foreach ($mappedData as $mappedDatum) {
            $csvData[] = $this->transformRow($mappedDatum);
        }

        return $csvData;
    }

    /**
     * @return string
     */
    public function getExportType() : string
    {
        return FtpManager::TRANSFER_TYPE_OPEN;
    }
}
