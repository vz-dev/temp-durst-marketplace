<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 17.01.18
 * Time: 13:02
 */

namespace Pyz\Zed\Sales\Business\Model\Order;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Sales\Persistence\SpySalesOrderTotals;
use Orm\Zed\Tax\Persistence\SpySalesOrderTaxRateTotal;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Locale\Persistence\LocaleQueryContainerInterface;
use Spryker\Zed\Sales\Business\Model\Order\OrderReferenceGeneratorInterface;
use Spryker\Zed\Sales\Business\Model\Order\SalesOrderSaver as SprykerSalesOrderSaver;
use Spryker\Zed\Sales\Business\Model\Order\SalesOrderSaverInterface as SprykerSalesOrderSaverInterface;
use Spryker\Zed\Sales\Business\Model\Order\SalesOrderSaverPluginExecutorInterface;
use Spryker\Zed\Sales\Business\Model\OrderItem\SalesOrderItemMapperInterface;
use Spryker\Zed\Sales\Dependency\Facade\SalesToCountryInterface;
use Spryker\Zed\Sales\Dependency\Facade\SalesToOmsInterface;
use Spryker\Zed\Sales\SalesConfig;

class OrderSaver extends SprykerSalesOrderSaver implements SprykerSalesOrderSaverInterface
{
    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    public function __construct(
        SalesToCountryInterface $countryFacade,
        SalesToOmsInterface $omsFacade,
        OrderReferenceGeneratorInterface $orderReferenceGenerator,
        SalesConfig $salesConfiguration,
        LocaleQueryContainerInterface $localeQueryContainer,
        Store $store,
        $orderExpanderPreSavePlugins,
        SalesOrderSaverPluginExecutorInterface $salesOrderSaverPluginExecutor,
        SalesOrderItemMapperInterface $salesOrderItemMapper,
        DeliveryAreaFacadeInterface $deliveryAreaFacade
    ) {
        parent::__construct(
            $countryFacade,
            $omsFacade,
            $orderReferenceGenerator,
            $salesConfiguration,
            $localeQueryContainer,
            $store,
            $orderExpanderPreSavePlugins,
            $salesOrderSaverPluginExecutor,
            $salesOrderItemMapper
        );

        $this->deliveryAreaFacade = $deliveryAreaFacade;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param SpySalesOrder $salesOrderEntity
     *
     * @return void
     */
    protected function hydrateSalesOrderEntity(QuoteTransfer $quoteTransfer, SpySalesOrder $salesOrderEntity)
    {
        $salesOrderEntity->setCustomerReference($quoteTransfer->getCustomer()->getCustomerReference());
        $this->hydrateSalesOrderCustomer($quoteTransfer, $salesOrderEntity);
        $salesOrderEntity->setPriceMode($quoteTransfer->getPriceMode());
        $salesOrderEntity->setStore($this->store->getStoreName());
        $salesOrderEntity->setCurrencyIsoCode($quoteTransfer->getCurrency()->getCode());
        $salesOrderEntity->setOrderReference($quoteTransfer->getOrderReference());
        if($quoteTransfer->getOrderReference() === null) {
            $salesOrderEntity->setOrderReference($this->orderReferenceGenerator->generateOrderReference($quoteTransfer));
        }
        $salesOrderEntity->setIsTest($this->salesConfiguration->isTestOrder($quoteTransfer));

        $this->hydrateSalesOrderEntityFromPlugins($quoteTransfer, $salesOrderEntity);
        if ($quoteTransfer->getCustomer() !== null && $quoteTransfer->getCustomer()->getHeidelpayRestId() !== null) {
            $salesOrderEntity->setHeidelpayRestCustomerId($quoteTransfer->getCustomer()->getHeidelpayRestId());
        }
        $salesOrderEntity->setDeliveryOrder($quoteTransfer->getDeliveryOrder());
        $salesOrderEntity->setIntegraReceiptDid($quoteTransfer->getIntegraReceiptDid());
        $salesOrderEntity->setIntegraReceiptNo($quoteTransfer->getIntegraReceiptNo());
        $salesOrderEntity->setIntegraCustomerNo($quoteTransfer->getIntegraCustomerNo());
        $salesOrderEntity->setIntegraPaymentMethod($quoteTransfer->getIntegraPaymentType());
        $salesOrderEntity->setIsExternal($quoteTransfer->getIsExternal());
        $salesOrderEntity->setFkBranch($quoteTransfer->getFkBranch());
        $salesOrderEntity->setFkConcreteTimeslot($quoteTransfer->getFkConcreteTimeSlot());
        $salesOrderEntity->setPlatform($quoteTransfer->getClientPlatform());
        $salesOrderEntity->setVersion($quoteTransfer->getClientVersion());
        $salesOrderEntity->setDeviceType($quoteTransfer->getDeviceType());

        if($quoteTransfer->getUseFlexibleTimeSlots() === true){
            $salesOrderEntity->setGmStartTime($this->toUtcDateTime($quoteTransfer->getStartTime()));
            $salesOrderEntity->setGmEndTime($this->toUtcDateTime($quoteTransfer->getEndTime()));
        }
    }

    /**
     * @param SpySalesOrder $salesOrderEntity
     * @param QuoteTransfer $quoteTransfer
     * @param SpySalesOrderItem $salesOrderItemEntity
     * @param ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function hydrateSalesOrderItemEntity(
        SpySalesOrder $salesOrderEntity,
        QuoteTransfer $quoteTransfer,
        SpySalesOrderItem $salesOrderItemEntity,
        ItemTransfer $itemTransfer
    ) {
        parent::hydrateSalesOrderItemEntity($salesOrderEntity, $quoteTransfer, $salesOrderItemEntity, $itemTransfer);

        $salesOrderItemEntity->setName(
            sprintf(
                '%s %s',
                $itemTransfer->getName(),
                $itemTransfer->getUnitName()
            )
        );
        $salesOrderItemEntity->setDepositAmount($itemTransfer->getUnitDeposit());
        $salesOrderItemEntity->setMerchantSku($itemTransfer->getMerchantSku());
        $salesOrderItemEntity->setRefundableAmount($itemTransfer->getSumPriceToPayAggregation());
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function saveOrderTotals(QuoteTransfer $quoteTransfer, $idSalesOrder)
    {
        $taxTotal = 0;
        if ($quoteTransfer->getTotals()->getTaxTotal()) {
            $taxTotal = $quoteTransfer->getTotals()->getTaxTotal()->getAmount();
        }

        $salesOrderTotalsEntity = new SpySalesOrderTotals();
        $salesOrderTotalsEntity->setFkSalesOrder($idSalesOrder);
        $salesOrderTotalsEntity->fromArray($quoteTransfer->getTotals()->toArray());
        $salesOrderTotalsEntity->setGrandTotal($quoteTransfer->getTotals()->getGrandTotal());
        $salesOrderTotalsEntity->setSubtotal($quoteTransfer->getTotals()->getSubtotal());
        $salesOrderTotalsEntity->setTaxTotal($taxTotal);
        $salesOrderTotalsEntity->setOrderExpenseTotal($quoteTransfer->getTotals()->getExpenseTotal());
        $salesOrderTotalsEntity->setRefundTotal($quoteTransfer->getTotals()->getGrandTotal());
        $salesOrderTotalsEntity->save();

        $this->saveTaxRateTotals($quoteTransfer, $salesOrderTotalsEntity->getIdSalesOrderTotals());
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrderTotals
     *
     * @return void
     */
    protected function saveTaxRateTotals(QuoteTransfer $quoteTransfer, int $idSalesOrderTotals): void
    {
        foreach ($quoteTransfer->getTotals()->getTaxRateTotals() as $taxRateTotal) {
            $taxRateTotalEntity = new SpySalesOrderTaxRateTotal();
            $taxRateTotalEntity->setFkSalesOrderTotals($idSalesOrderTotals);
            $taxRateTotalEntity->setTaxRate($taxRateTotal->getRate());
            $taxRateTotalEntity->setTaxTotal($taxRateTotal->getAmount());
            $taxRateTotalEntity->save();
        }
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     */
    private function toUtcDateTime(string $dateTime): DateTime
    {
        return (new DateTime($dateTime, new DateTimeZone('Europe/Berlin')))
            ->setTimezone(new DateTimeZone('UTC'));
    }
}
