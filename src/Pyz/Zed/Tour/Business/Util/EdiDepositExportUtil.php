<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-14
 * Time: 12:21
 */

namespace Pyz\Zed\Tour\Business\Util;

use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\ConcreteTourInterface;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class EdiDepositExportUtil extends EdiExportUtil
{
    public const KEY_SALES_EXPENSE_QUANTITY = 'KEY_SALES_EXPENSE_QUANTITY';
    public const KEY_SALES_EXPENSE_IS_NEGATIVE = 'KEY_SALES_EXPENSE_IS_NEGATIVE';
    public const KEY_SALES_EXPENSE_ID = 'KEY_SALES_EXPENSE_ID';
    public const KEY_SALES_EXPENSE_TYPE = 'KEY_SALES_EXPENSE_TYPE';
    public const KEY_SALES_EXPENSE_MERCHANT_SKU = 'KEY_SALES_EXPENSE_MERCHANT_SKU';
    public const KEY_SALES_EXPENSE_NAME = 'KEY_SALES_EXPENSE_NAME';
    public const KEY_SALES_EXPENSE_FK_SALES_ORDER = 'KEY_SALES_EXPENSE_FK_SALES_ORDER';

    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @var RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactFacade;

    /**
     * EdiDepositExportUtil constructor.
     * @param int $idTour
     * @param ConcreteTourInterface $concreteTour
     * @param TourConfig $tourConfig
     * @param TourQueryContainerInterface $tourQueryContainer
     * @param OmsQueryContainerInterface $omsQueryContainer
     * @param SalesQueryContainerInterface $salesQueryContainer
     * @param EdifactReferenceGenerator $ediReferenceGenerator
     * @param RefundFacadeInterface $refundFacade
     * @param BillingFacadeInterface $billingFacade
     * @param EdifactFacadeInterface $edifactFacade
     * @param GraphMastersQueryContainerInterface $graphMastersQueryContainer
     * @param bool $isGraphmastersTour
     */
    public function __construct(
        int $idTour,
        ConcreteTourInterface $concreteTour,
        TourConfig $tourConfig,
        TourQueryContainerInterface $tourQueryContainer,
        OmsQueryContainerInterface $omsQueryContainer,
        SalesQueryContainerInterface $salesQueryContainer,
        EdifactReferenceGenerator $ediReferenceGenerator,
        RefundFacadeInterface $refundFacade,
        BillingFacadeInterface $billingFacade,
        EdifactFacadeInterface $edifactFacade,
        GraphMastersQueryContainerInterface $graphMastersQueryContainer,
        GraphMastersFacadeInterface $graphMastersFacade,
        bool $isGraphmastersTour = false
    )
    {
        $this->salesQueryContainer = $salesQueryContainer;
        $this->refundFacade = $refundFacade;
        $this->edifactFacade = $edifactFacade;

        parent::__construct(
            $idTour,
            $concreteTour,
            $tourQueryContainer,
            $omsQueryContainer,
            $tourConfig,
            $ediReferenceGenerator,
            $billingFacade,
            $graphMastersQueryContainer,
            $graphMastersFacade,
            $isGraphmastersTour
        );
    }

    /**
     * @return array
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getConsolidatedDeposits(): array
    {
        $exportVersion = $this->edifactFacade->getExportVersion();

        $orders = ($this->isGraphmastersTour === true)
            ? $this->getSalesOrdersForGraphmastersTour()
            : $this->getSalesOrdersForConcreteTour();

        $products = $this->getSalesExpensesByFkSalesOrders($orders);

        $exportArray = [];

        if ($exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
            $exportArray = $this->consolidateProductsBySku($products);
        } else if ($exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $orderArray = parent::getOrderDataFromSalesOrders($orders);

            $exportArray = $this->mergeOrdersAndProducts($orderArray, $products);
        }

        return $exportArray;
    }

    /**
     * @param array $salesOrders
     * @return array
     * @throws AmbiguousComparisonException
     */
    protected function getSalesExpensesByFkSalesOrders(array $salesOrders): array
    {
        $fkSalesOrders = ($this->isGraphmastersTour === true)
            ? parent::getFkSalesOrdersForGraphmastersTour()
            : parent::getFkSalesOrdersForConcreteTour();

        $salesExpenses = $this
            ->salesQueryContainer
            ->queryDepositReturnSalesExpensesByOrderIds($fkSalesOrders)
            ->find();

        $result = [];

        /*  @var $salesExpense SpySalesExpense */
        foreach ($salesExpenses as $salesExpense) {
            $result[$salesExpense->getMerchantSku()][] = [
                static::KEY_SALES_EXPENSE_ID => $salesExpense->getIdSalesExpense(),
                static::KEY_SALES_EXPENSE_IS_NEGATIVE => $salesExpense->getIsNegative(),
                static::KEY_SALES_EXPENSE_MERCHANT_SKU => $salesExpense->getMerchantSku(),
                static::KEY_SALES_EXPENSE_NAME => $salesExpense->getName(),
                static::KEY_SALES_EXPENSE_QUANTITY => $salesExpense->getQuantity(),
                static::KEY_SALES_EXPENSE_TYPE => $salesExpense->getType(),
                static::KEY_SALES_EXPENSE_FK_SALES_ORDER => $salesExpense->getFkSalesOrder(),
            ];
        }

        $salesRefunds = $this
            ->refundFacade
            ->getSalesOrderRefundsBySalesOrderIds(
                $fkSalesOrders,
                $this
                    ->branchTransfer
                    ->getEdiExcludeMissingItemReturns()
                ?? EdifactConstants::EDIFACT_EXCLUDE_MISSING_ITEM_RETURNS_DEFAULT
            );

        foreach($salesRefunds as $salesRefund) {
            if($salesRefund->getMerchantSku() !== null){
                $result[$salesRefund->getMerchantSku()][] = [
                    static::KEY_SALES_EXPENSE_MERCHANT_SKU => $salesRefund->getMerchantSku(),
                    static::KEY_SALES_EXPENSE_NAME => $salesRefund->getComment(),
                    static::KEY_SALES_EXPENSE_QUANTITY => $salesRefund->getQuantity(),
                    static::KEY_SALES_EXPENSE_FK_SALES_ORDER => $salesRefund->getFkSalesOrder(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param array $allProducts
     * @return array
     */
    protected function consolidateProductsBySku(array $allProducts):  array
    {
        $consolidatedProducts = [];

        foreach ($allProducts as $sku => $allProduct) {
            $quantities = array_column($allProduct, static::KEY_SALES_EXPENSE_QUANTITY);
            $name = array_column($allProduct, static::KEY_SALES_EXPENSE_NAME);
            $sum = 0;

            foreach ($quantities as $quantity) {
                $sum += $quantity;
            }

            $consolidatedProducts[] = [
                TourExportMapper::PAYLOAD_MERCHANT_SKU => $sku,
                TourExportMapper::PAYLOAD_QUANTITY => $sum,
                TourExportMapper::PAYLOAD_DURST_SKU => '',
                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => reset($name),
                TourExportMapper::PAYLOAD_GTIN => $sku
            ];
        }

        return $consolidatedProducts;
    }

    /**
     * @return array
     */
    protected function getStateIds(): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName(
                $this
                    ->tourConfig
                    ->getDeliveredOrPayedOmsState()
            )
            ->find();

        $states = [];

        foreach ($result as $item) {
            $states[] = $item->getIdOmsOrderItemState();
        }

        return $states;
    }

    /**
     * @return ObjectCollection
     * @throws AmbiguousComparisonException
     */
    protected function getOrderItemsForConcreteTour(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->queryOrderItemsForConcreteTourWithDeliveryStatus($this->idTour)
            ->joinWithOrder()
            ->find();
    }

    /**
     * @return array
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    protected function getSalesOrdersForConcreteTour(): array
    {
        $orderItems = $this->getOrderItemsForConcreteTour();

        $orders = [];

        /* @var $orderItem SpySalesOrderItem */
        foreach ($orderItems as $orderItem) {
            $order = $orderItem->getOrder();

            if (in_array($order, $orders) === false) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    /**
     * @return array
     * @throws PropelException
     */
    protected function getSalesOrdersForGraphmastersTour(): array
    {
        $orderItems = $this->getOrderItemsForGraphmastersTour();

        $orders = [];

        /* @var $orderItem SpySalesOrderItem */
        foreach ($orderItems as $orderItem) {
            $order = $orderItem->getOrder();

            if (in_array($order, $orders) === false) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    /**
     * @param array $orders
     * @param array $items
     * @return array
     */
    protected function mergeOrdersAndProducts(array $orders, array $items): array
    {
        $exportData = [];

        foreach ($orders as $order) {
            $mergedOrderItems = [];

            foreach ($items as $sku => $skuItems) {
                foreach ($skuItems as $orderItem) {
                    if ($orderItem[self::KEY_SALES_EXPENSE_FK_SALES_ORDER] === $order[TourExportMapper::PAYLOAD_ORDER_ID]) {
                        if (!in_array(
                            $orderItem[self::KEY_SALES_EXPENSE_MERCHANT_SKU],
                            array_keys($mergedOrderItems)
                        )) {
                            $mergedOrderItems[$orderItem[self::KEY_SALES_EXPENSE_MERCHANT_SKU]] = [
                                TourExportMapper::PAYLOAD_MERCHANT_SKU => $sku,
                                TourExportMapper::PAYLOAD_QUANTITY => $orderItem[self::KEY_SALES_EXPENSE_QUANTITY],
                                TourExportMapper::PAYLOAD_DURST_SKU => '',
                                TourExportMapper::PAYLOAD_PRODUCT_DESCRIPTION => $orderItem[self::KEY_SALES_EXPENSE_NAME],
                                TourExportMapper::PAYLOAD_GTIN => $sku,
                                TourExportMapper::PAYLOAD_ORDER_ITEM_ORDER_REFERENCE => $order[TourExportMapper::PAYLOAD_ORDER_REFERENCE],
                                TourExportMapper::PAYLOAD_ORDER_ITEM_PRICE_TO_PAY => null,
                                TourExportMapper::PAYLOAD_ORDER_ITEM_FK_SALES_ORDER => $orderItem[self::KEY_SALES_EXPENSE_FK_SALES_ORDER],
                            ];
                        } else {
                            $mergedOrderItems[$orderItem[self::KEY_SALES_EXPENSE_MERCHANT_SKU]][TourExportMapper::PAYLOAD_QUANTITY] += $orderItem[self::KEY_SALES_EXPENSE_QUANTITY];
                        }
                    }
                }
            }

            $order[TourExportMapper::PAYLOAD_ORDER_ITEMS] = array_values($mergedOrderItems);

            $exportData[] = [
                TourExportMapper::PAYLOAD_ORDER_ID => $order[TourExportMapper::PAYLOAD_ORDER_ID],
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => $order[TourExportMapper::PAYLOAD_ORDER_REFERENCE],
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => $order[TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE],
                TourExportMapper::PAYLOAD_ORDER_ITEMS => $order[TourExportMapper::PAYLOAD_ORDER_ITEMS],
            ];
        }

        return $exportData;
    }
}
