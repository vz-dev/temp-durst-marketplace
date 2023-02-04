<?php
/**
 * Durst - project - ClosedOrdersExport.php.
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
use Orm\Zed\Oms\Persistence\Map\SpyOmsOrderItemStateTableMap;
use Orm\Zed\Refund\Persistence\Map\SpyRefundTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesExpenseTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderCommentTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderItemTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Pyz\Zed\Integra\Business\Exception\InvalidValueException;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManager;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class ClosedOrdersExport extends AbstractExport
{
    protected const STATE = 'CLSD';

    protected const DURST_ITEM_SATES_TO_INTEGRA = [
        'damaged' => 35,
        'declined' => 35,
        'missing' => 35,
        'canceled driver' => 35,
        'canceled driver early' => 35,
        'canceled user' => 35,
    ];

    protected const GBZ_LEERGUT_POSITION = 25;
    protected const REFUND_STAT_NAMES = [
        'damaged',
        'declined',
        'missing',
        'canceled driver',
        'canceled driver early',
        'canceled user',
    ];

    /**
     * @var array
     */
    protected $refunds = [];

    /**
     * @var array
     */
    protected $expenses = [];

    /**
     * @var array
     */
    protected $ordersNoRefundsExpenses = [];

    /**
     * @var array
     */
    protected $itemsPos = [];

    /**
     * ClosedOrdersExport constructor.
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
        $this->fillRefunds($idBranch);
        $data = $this->collectData($idBranch);
        return $this->mapData($data);
    }

    /**
     * @param int $idBranch
     * @return array
     */
    public function getExternalIdOrdersByBranchId(int $idBranch) : array
    {
        return $this
            ->queryContainer
            ->queryIdOrdersForExternalClosableOrdersByBranchId($idBranch)
            ->find()
            ->toArray();
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
            $this->queryContainer->updateOrdersClosed($this->affectedOrderIds)
        );
    }

    /**
     * @param int $idBranch
     *
     * @return void
     */
    protected function fillRefunds(int $idBranch): void
    {
        $this->refunds = [];
        foreach ($this->collectRefunds($idBranch) as $refund) {
            $this->refunds[$refund[SpyRefundTableMap::COL_FK_SALES_ORDER]][$refund[SpyRefundTableMap::COL_MERCHANT_SKU]] =
                $refund[IntegraQueryContainerInterface::VIRTUAL_COL_QUANTITY];
        }
    }

    /**
     * @param int $idSalesOrder
     * @param string $merchantSku
     * @param int $quantityWithoutRefunds
     *
     * @return int
     */
    protected function getQuantityWithRefunds(int $idSalesOrder, string $merchantSku, int $quantityWithoutRefunds): int
    {
        if (array_key_exists($idSalesOrder, $this->refunds) !== true ||
            array_key_exists($merchantSku, $this->refunds[$idSalesOrder]) !== true) {
            return $quantityWithoutRefunds;
        }

        $quantity = $quantityWithoutRefunds - $this->refunds[$idSalesOrder][$merchantSku];
        if ($quantity < 0) {
            InvalidValueException::negative('quantity');
        }
        return $quantity;
    }

    /**
     * @param int $idBranch
     *
     * @return array
     */
    protected function collectRefunds(int $idBranch): array
    {
        return $this
            ->queryContainer
            ->queryRefundsForClosedOrders($idBranch)
            ->find()
            ->toArray();
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
            ->queryClosableOrdersForBranch($idBranch)
            ->find()
            ->toArray();
    }

    /**
     * @return iterable
     */
    protected function collectExpenses(): iterable
    {
        return $this
            ->queryContainer
            ->queryExpensesForOrders($this->affectedOrderIds)
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
        $this->mapItems($data, $mappedData);
        $expenseData = $this->collectExpenses();
        $this->mapExpenses($expenseData, $mappedData);
        $this->mapItemsOrderWoDepositsOrRefunds($data, $mappedData);

        usort($mappedData, function ($a, $b): int {
            if ($a[static::HEADER_ORDER_NO] === $b[static::HEADER_ORDER_NO]) {
                return $a[static::HEADER_POSITION_NO] <=> $b[static::HEADER_POSITION_NO];
            }
            return $this->getStringWithNumbersOnly($a[static::HEADER_ORDER_NO]) <=> $this->getStringWithNumbersOnly($b[static::HEADER_ORDER_NO]);
        });

        $csvData = [];
        $csvData[] = static::HEADER;
        foreach ($mappedData as $mappedDatum) {
            $csvData[] = $this->transformRow($mappedDatum);
        }

        return $csvData;
    }

    /**
     * @param iterable $data
     * @param array $mappedData
     *
     * @return void
     */
    protected function mapItems(
        iterable &$data,
        array &$mappedData
    ): void {
        foreach ($data as $datum) {
            if (in_array($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->affectedOrderIds) !== true) {
                $this->affectedOrderIds[] = $datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER];
            }

            if(in_array($datum[SpyOmsOrderItemStateTableMap::COL_NAME], self::REFUND_STAT_NAMES) !== true)
            {
               continue;
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
                static::HEADER_POSITION_NO => $this->getPosByOrderIdAndSkuState($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU], $datum[SpyOmsOrderItemStateTableMap::COL_NAME]),
                static::HEADER_SKU => $this->getStringWithNumbersOnly($datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU]),
                static::HEADER_PRODUCT => $datum[SpySalesOrderItemTableMap::COL_NAME],
                static::HEADER_QUANTITY => $datum[IntegraQueryContainerInterface::VIRTUAL_COL_QUANTITY],
                static::HEADER_TYPE => $this->getUnitTypeFromMerchantSku($datum[SpySalesOrderItemTableMap::COL_MERCHANT_SKU]),
                static::HEADER_STATE => static::STATE,
                static::HEADER_TRANSACTIONCODE_UNZER => $datum[IntegraQueryContainerInterface::VIRTUAL_COL_HEIDELPAY_SHORT_ID],
                static::HEADER_RETURN_REASON => $this->getIntegraReturnReasonFromItemState($datum[SpyOmsOrderItemStateTableMap::COL_NAME]),
                static::HEADER_TOUR_TRIP_NO => $this->getTourTripNoFromDateTime($datum[SpyConcreteTimeSlotTableMap::COL_START_TIME], $datum[SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO]),
                static::HEADER_AMOUNT_PAID => $datum[SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID],
            ];
        }
    }

    /**
     * @param iterable $expenses
     * @param array $mappedData
     *
     * @return void
     */
    protected function mapExpenses(
        iterable &$expenses,
        array &$mappedData
    ): void {
        foreach ($expenses as $expense) {
            if (in_array($expense[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->expenses) !== true) {
                $this->expenses[] = $expense[SpySalesOrderTableMap::COL_ID_SALES_ORDER];
            }

            $mappedData[] = [
                static::HEADER_ORDER_NO => $expense[SpySalesOrderTableMap::COL_ORDER_REFERENCE],
                static::HEADER_RECEIPT_DID => $expense[SpySalesOrderTableMap::COL_INTEGRA_RECEIPT_DID],
                static::HEADER_SHIPMENT_TYPE => $this->getVersandartByDateAndZip($expense[SpyConcreteTimeSlotTableMap::COL_START_TIME], $expense[SpyDeliveryAreaTableMap::COL_ZIP_CODE], $expense[SpyTimeSlotTableMap::COL_INTEGRA_TOUR_NO]),
                static::HEADER_TOUR_NR => '',
                static::HEADER_ORDER_DATE => $this->getFormattedDate($expense[SpySalesOrderTableMap::COL_CREATED_AT]),
                static::HEADER_DELIVERY_START => $this->getBerlinEuropeTimeFromUtc($expense[SpyConcreteTimeSlotTableMap::COL_START_TIME]),
                static::HEADER_DELIVERY_END => $this->getBerlinEuropeTimeFromUtc($expense[SpyConcreteTimeSlotTableMap::COL_END_TIME]),
                static::HEADER_COMMENT => $this->stripIllegalChars($expense[SpySalesOrderCommentTableMap::COL_MESSAGE]),
                static::HEADER_SELLER_GLN => static::SELLER_GLN,
                static::HEADER_CUSTOMER_NO => $expense[SpySalesOrderTableMap::COL_INTEGRA_CUSTOMER_NO],
                static::HEADER_POSITION_DID => static::NA_STRING,
                static::HEADER_POSITION_NO => $this->getPosByOrderIdAndSkuState($expense[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $expense[SpySalesExpenseTableMap::COL_TYPE], ''),
                static::HEADER_SKU => $expense[SpySalesExpenseTableMap::COL_MERCHANT_SKU],
                static::HEADER_PRODUCT => $expense[SpySalesExpenseTableMap::COL_NAME],
                static::HEADER_QUANTITY => $expense[SpySalesExpenseTableMap::COL_QUANTITY],
                static::HEADER_TYPE => static::UNIT_TYPE_CASE,
                static::HEADER_STATE => static::STATE,
                static::HEADER_TRANSACTIONCODE_UNZER => $expense[IntegraQueryContainerInterface::VIRTUAL_COL_HEIDELPAY_SHORT_ID],
                static::HEADER_RETURN_REASON => static::GBZ_LEERGUT_POSITION,
                static::HEADER_TOUR_TRIP_NO => $this->getTourTripNoFromDateTime($expense[SpyConcreteTimeSlotTableMap::COL_START_TIME], $expense[SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO]),
                static::HEADER_AMOUNT_PAID => $expense[SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID],
            ];
        }
    }

    /**
     * @param iterable $data
     * @param array $mappedData
     */
    protected function mapItemsOrderWoDepositsOrRefunds(
        iterable &$data,
        array &$mappedData
    ) : void {
        foreach ($data as $datum) {
            if(
                in_array($datum[SpyOmsOrderItemStateTableMap::COL_NAME], self::REFUND_STAT_NAMES) === true ||
                array_key_exists($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->refunds) === true ||
                in_array($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->expenses) === true ||
                in_array($datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER], $this->ordersNoRefundsExpenses) === true
            )
            {
                continue;
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
                static::HEADER_POSITION_DID => '',
                static::HEADER_POSITION_NO => '',
                static::HEADER_SKU => '',
                static::HEADER_PRODUCT => '',
                static::HEADER_QUANTITY => '',
                static::HEADER_TYPE => '',
                static::HEADER_STATE => '',
                static::HEADER_TRANSACTIONCODE_UNZER => $datum[IntegraQueryContainerInterface::VIRTUAL_COL_HEIDELPAY_SHORT_ID],
                static::HEADER_RETURN_REASON => '',
                static::HEADER_TOUR_TRIP_NO => $this->getTourTripNoFromDateTime($datum[SpyConcreteTimeSlotTableMap::COL_START_TIME], $datum[SpyTimeSlotTableMap::COL_INTEGRA_DELIVERY_WINDOW_NO]),
                static::HEADER_AMOUNT_PAID => $datum[SpySalesOrderTableMap::COL_EXTERNAL_AMOUNT_PAID],
            ];

            $this->ordersNoRefundsExpenses[] = $datum[SpySalesOrderTableMap::COL_ID_SALES_ORDER];
        }
    }

    public function getExportType() : string
    {
        return FtpManager::TRANSFER_TYPE_CLOSED;
    }

    /**
     * @param string $itemState
     * @return string
     */
    protected function getIntegraReturnReasonFromItemState(string $itemState) : string
    {
        if(array_key_exists($itemState, self::DURST_ITEM_SATES_TO_INTEGRA))
        {
            return self::DURST_ITEM_SATES_TO_INTEGRA[$itemState];
        }

        return '';
    }
}
