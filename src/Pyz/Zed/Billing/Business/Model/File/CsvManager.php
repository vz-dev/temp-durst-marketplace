<?php
/**
 * Durst - project - CsvManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.05.20
 * Time: 16:24
 */

namespace Pyz\Zed\Billing\Business\Model\File;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Orm\Zed\Billing\Persistence\DstBillingPeriod;
use Orm\Zed\HeidelpayRest\Persistence\Map\DstPaymentHeidelpayRestLogTableMap;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Refund\Persistence\SpyRefund;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException;

class CsvManager extends AbstractCsvExportManager implements CsvManagerInterface
{
    protected const FILE_NAME_PATTERN = 'billing_%s_%d.csv';
    protected const DELIMITER = ';';

    protected const BRANCH = 'Standort';
    protected const TYPE = 'Art';
    protected const INVOICE_NUMBER = 'Rechnungsnr.';
    protected const TOUR_NUMBER = 'Tournr.';
    protected const EDI_NUMBER = 'EDI-Nr.';
    protected const SHIPMENT_DATE = 'Lieferdatum';
    protected const SHIPMENT_DATE_FORMAT = 'd.m.Y';
    protected const INVOICE_DATE = 'Rechnungsdatum';
    protected const INVOICE_DATE_FORMAT = 'd.m.Y';
    protected const DURST_SKU = 'Artikelnr. Durst';
    protected const MERCHANT_SKU = 'Artikelnr. GFGH';
    protected const QUANTITY = 'Anzahl';
    protected const LABEL = 'Bezeichnung';
    protected const PRICE_NET = 'Einzelpreis Netto (cent)';
    protected const SUM_GROSS = 'Gesamtpreis Brutto (cent)';
    protected const TAX_RATE = 'Steuersatz';
    protected const SUM_DISCOUNT_GROSS = 'Gesamtrabatt Brutto (cent)';
    protected const ZIP_CODE = 'PLZ';
    protected const TIME_SLOT = 'Lieferzeitfenster';
    protected const TIME_SLOT_FORMAT = '%s - %s';
    protected const TIME_SLOT_TIME_FORMAT = 'H:i';
    protected const PAYMENT_TYPE = 'Zahlart';
    protected const ORDER_DATE = 'Bestelleingang';
    protected const ORDER_DATE_FORMAT = 'd.m.Y H:i';
    protected const HEIDELPAY_SHORT_ID = 'Heidelpay Short-ID';
    protected const BILLING_REFERENCE = 'Sammelbelegs-Nr';

    protected const PAYMENT_TYPE_UNKNOWN = 'unknown';

    protected const TYPE_ITEM = 'Getränkeverkauf';
    protected const TYPE_DEPOSIT = 'Pfand';
    protected const TYPE_RETURNED_DEPOSIT = 'Leergut';
    protected const TYPE_RETOURE = 'Retoure Getränkeverkauf';
    protected const TYPE_RETOURE_DEPOSIT = 'Retoure Pfand';
    protected const TYPE_VOUCHER_DISCOUNT = 'Gutschein/Nachlass';

    protected const EXPENSE_TYPE_VOUCHER_DISCOUNT = 'VOUCHER_CODE_EXPENSE_TYPE';

    protected const NOT_AVAILABLE = 'N/A';

    /**
     * @var BillingQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var BillingConfig
     */
    protected $config;

    /**
     * @var PathManagerInterface
     */
    protected $pathManager;

    /**
     * @var BillingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphmastersFacade;

    /**
     * @var array
     */
    protected $billingPeriodAssoc = [];

    /**
     * @param BillingQueryContainerInterface $queryContainer
     * @param BillingConfig $config
     * @param PathManagerInterface $pathManager
     * @param BillingToMerchantBridgeInterface $merchantFacade
     * @param GraphMastersFacadeInterface $graphMastersFacade
     */
    public function __construct(
        BillingQueryContainerInterface $queryContainer,
        BillingConfig $config,
        PathManagerInterface $pathManager,
        BillingToMerchantBridgeInterface $merchantFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->pathManager = $pathManager;
        $this->merchantFacade = $merchantFacade;
        $this->graphmastersFacade = $graphMastersFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     */
    public function createCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        try {
            $billingPeriodEntity = $this->getEntityEagerLoaded($billingPeriodTransfer->getIdBillingPeriod());
            $this->prepareBillingPeriodAssocArray($billingPeriodEntity);
        } catch (BillingPeriodEntityNotFoundException $e) {
            // if any information is missing we just save a csv file with the header
        }
        return $this->saveCsvFile($billingPeriodTransfer->getBillingReference(), $billingPeriodTransfer->getBranch()->getIdBranch());
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return DstBillingPeriod
     */
    protected function getEntityEagerLoaded(int $idBillingPeriod): DstBillingPeriod
    {
        $billingPeriodEntity = $this
            ->queryContainer
            ->queryInvoiceInformationForBillingPeriodById($idBillingPeriod)
            ->findOne();

        if ($billingPeriodEntity === null) {
            throw BillingPeriodEntityNotFoundException::createInvoiceInformation($idBillingPeriod);
        }

        return $billingPeriodEntity;
    }

    /**
     * @param DstBillingPeriod $billingPeriod
     *
     * @return void
     */
    protected function prepareBillingPeriodAssocArray(DstBillingPeriod $billingPeriod): void
    {
        foreach ($billingPeriod->getDstBillingItems() as $billingItem) {
            $orderInformation = $this->getOrderInformation($billingItem->getSpySalesOrder());
            $orderInformation[static::BILLING_REFERENCE] = $billingPeriod->getBillingReference();
            $paymentType = $this->prepareItemInformation($billingItem->getSpySalesOrder()->getItems(), $orderInformation);
            $this->prepareExpenseInformation($billingItem->getSpySalesOrder()->getExpenses(), $orderInformation, $paymentType);
            $this->prepareRefundInformation($billingItem->getSpySalesOrder()->getSpyRefunds(), $orderInformation, $paymentType);
            $this->prepareVoucherInformation($billingItem->getSpySalesOrder()->getExpenses(), $orderInformation, $paymentType);
        }
    }

    /**
     * @param iterable|SpySalesOrderItem[] $items
     * @param array $orderInformation
     *
     * @return string
     */
    protected function prepareItemInformation(iterable $items, array $orderInformation): string
    {
        $paymentType = static::PAYMENT_TYPE_UNKNOWN;

        $groupedItems = $this->groupItems($items);

        foreach ($groupedItems as $item) {
            $row = $orderInformation;
            $row[static::TYPE] = static::TYPE_ITEM;
            $row[static::MERCHANT_SKU] = $item->getMerchantSku();
            $row[static::LABEL] = $item->getName();
            $row[static::DURST_SKU] = $item->getSku();
            $row[static::QUANTITY] = $item->getQuantity();
            $row[static::PRICE_NET] = $item->getNetPrice();
            $row[static::TAX_RATE] = $item->getTaxRate();
            $row[static::SUM_GROSS] = $item->getPrice() * $item->getQuantity();
            $row[static::SUM_DISCOUNT_GROSS] = $item->getDiscountAmountAggregation() * $item->getQuantity();
            $paymentType = $this->mapPaymentType($item);
            $row[static::PAYMENT_TYPE] = $this->mapPaymentType($item);
            $this->billingPeriodAssoc[] = $row;
        }

        return $paymentType;
    }

    /**
     * @param iterable|SpySalesOrderItem[] $items
     *
     * @return SpySalesOrderItem[]
     */
    protected function groupItems(iterable $items): array
    {
        $groupedItems = [];
        foreach ($items as $item) {
            if (array_key_exists($item->getSku(), $groupedItems) === false) {
                $groupedItems[$item->getSku()] = $item;
                continue;
            }

            /** @var SpySalesOrderItem $groupedItem */
            $groupedItem = $groupedItems[$item->getSku()];
            $groupedItem->setQuantity($groupedItem->getQuantity() + 1);
        }

        return $groupedItems;
    }

    /**
     * @param iterable|SpySalesExpense[] $expenses
     *
     * @return SpySalesExpense[]
     */
    protected function groupExpenses(iterable $expenses): array
    {
        $groupedExpenses = [];
        foreach ($expenses as $expense) {
            if ($this->isExpenseDeposit($expense->getType()) !== true) {
                $groupedExpenses[] = $expense;
                continue;
            }

            $sku = (explode('-', $expense->getType()))[1];
            if (array_key_exists($sku, $groupedExpenses) === false) {
                $groupedExpenses[$sku] = $expense;
                continue;
            }

            /** @var SpySalesExpense $groupedExpense */
            $groupedExpense = $groupedExpenses[$sku];
            $groupedExpense->setQuantity($groupedExpense->getQuantity() + 1);
        }

        return $groupedExpenses;
    }

    /**
     * @param SpySalesOrderItem $orderItem
     * @return string
     * @throws PropelException
     */
    protected function mapPaymentType(SpySalesOrderItem $orderItem): string
    {
        $processName = $orderItem
            ->getProcess()
            ->getName();

        $paymentTypeMap = $this->config->getCsvExportPaymentTypeMap();

        if (array_key_exists($processName, $paymentTypeMap) === true) {
            return $paymentTypeMap[$processName];
        }

        try {
            return $this->getPaymentMethodName($orderItem);
        } catch (PaymentMethodNotFoundException $exception) {
            return static::PAYMENT_TYPE_UNKNOWN;
        }
    }

    /**
     * @param iterable|SpySalesExpense[] $expenses
     * @param array $orderInformation
     * @param string $paymentType
     *
     * @return void
     */
    protected function prepareExpenseInformation(iterable $expenses, array $orderInformation, string $paymentType): void
    {
        $groupedExpenses = $this->groupExpenses($expenses);

        foreach ($groupedExpenses as $expense) {
            if (($this->isExpenseReturnedDeposit($expense->getType()) !== true) &&
                ($this->isExpenseDeposit($expense->getType()) !== true)
            ) {
                continue;
            }
            $this->transformNegative($expense);
            $row = $orderInformation;
            $row[static::PRICE_NET] = $expense->getNetPrice();
            $row[static::SUM_GROSS] = $expense->getPrice() * $expense->getQuantity();
            $row[static::SUM_DISCOUNT_GROSS] = static::NOT_AVAILABLE;
            $row[static::LABEL] = $expense->getName();
            $row[static::QUANTITY] = $expense->getQuantity();
            $row[static::TAX_RATE] = $expense->getTaxRate();
            $row[static::DURST_SKU] = static::NOT_AVAILABLE;
            $row[static::PAYMENT_TYPE] = $paymentType;
            $row[static::MERCHANT_SKU] = static::NOT_AVAILABLE;
            if ($this->isExpenseReturnedDeposit($expense->getType())) {
                $row[static::TYPE] = static::TYPE_RETURNED_DEPOSIT;
                $row[static::EDI_NUMBER] = sprintf(
                    '2_%s',
                    $row[static::TOUR_NUMBER]
                );
                $row[static::MERCHANT_SKU] = $expense->getMerchantSku();
                $this->billingPeriodAssoc[] = $row;
                continue;
            }
            $row[static::TYPE] = static::TYPE_DEPOSIT;
            $this->billingPeriodAssoc[] = $row;
        }
    }

    /**
     * @param iterable|SpySalesExpense[] $expenses
     * @param array $orderInformation
     * @param string $paymentType
     *
     * @return void
     */
    protected function prepareVoucherInformation(iterable $expenses, array $orderInformation, string $paymentType): void
    {
        foreach ($expenses as $expense) {
            if ($this->isVoucherDiscount($expense->getType()) !== true) {
                continue;
            }
            $this->transformNegative($expense);
            $row = $orderInformation;
            $row[static::PRICE_NET] = 0;
            $row[static::SUM_GROSS] = 0;
            $row[static::SUM_DISCOUNT_GROSS] = $expense->getDiscountAmountAggregation();
            $row[static::LABEL] = $expense->getName();
            $row[static::QUANTITY] = $expense->getQuantity();
            $row[static::TAX_RATE] = $expense->getTaxRate();
            $row[static::DURST_SKU] = static::NOT_AVAILABLE;
            $row[static::PAYMENT_TYPE] = $paymentType;
            $row[static::MERCHANT_SKU] = static::NOT_AVAILABLE;
            $row[static::TYPE] = static::TYPE_VOUCHER_DISCOUNT;
            $this->billingPeriodAssoc[] = $row;
        }
    }

    /**
     * @param SpySalesExpense $expense
     *
     * @return void
     */
    protected function transformNegative(SpySalesExpense $expense): void
    {
        if ($expense->getIsNegative() === true) {
            $expense->setPrice($expense->getPrice() * -1);
            $expense->setNetPrice($expense->getNetPrice() * -1);
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isExpenseDeposit(string $type): bool
    {
        return (substr($type, 0, 7) === 'deposit');
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isExpenseReturnedDeposit(string $type): bool
    {
        return (substr($type, 0, 8) === 'RETURNED');
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function isVoucherDiscount(string $type): bool
    {
        return ($type === static::EXPENSE_TYPE_VOUCHER_DISCOUNT);
    }

    /**
     * @param iterable|SpyRefund[] $refunds
     * @param array $orderInformation
     * @param string $paymentType
     *
     * @return void
     */
    protected function prepareRefundInformation(iterable $refunds, array $orderInformation, string $paymentType): void
    {
        foreach ($refunds as $refund) {
            $negativeAmount = $refund->getAmount() * -1;
            $row = $orderInformation;
            $row[static::TYPE] = static::TYPE_RETOURE;
            if ($this->isRefundDeposit($refund) === true) {
                $row[static::TYPE] = static::TYPE_RETOURE_DEPOSIT;
            }

            if ($this->isRefundDeposit($refund) === false) {
                $item = $refund->getSpySalesOrderItem();
                $expense = null;
            } else {
                $item = null;
                $expense = $refund->getSpySalesExpense();
            }

            if ($item !== null) {
                $row[static::PRICE_NET] = $item->getNetPrice() * -1;
            } else if ($expense !== null) {
                $row[static::PRICE_NET] = $expense->getNetPrice() * -1;
            } else {
                $row[static::PRICE_NET] = 0;
            }

            $row[static::SUM_GROSS] = ($item !== null)
                ? ($negativeAmount - $item->getDiscountAmountAggregation()) * $refund->getQuantity()
                : $negativeAmount * $refund->getQuantity();

            $row[static::SUM_DISCOUNT_GROSS] = ($item !== null)
                ? $item->getDiscountAmountAggregation() * -1 * $refund->getQuantity()
                : static::NOT_AVAILABLE;

            $row[static::LABEL] = $refund->getComment();
            $row[static::QUANTITY] = $refund->getQuantity();

            if ($item !== null) {
                $row[static::TAX_RATE] = $item->getTaxRate();
            } else if ($expense !== null) {
                $row[static::TAX_RATE] = $expense->getTaxRate();
            } else {
                $row[static::TAX_RATE] = static::NOT_AVAILABLE;
            }

            $row[static::MERCHANT_SKU] = $refund->getMerchantSku();
            $row[static::DURST_SKU] = $refund->getSku();
            $row[static::PAYMENT_TYPE] = $paymentType;
            $row[static::EDI_NUMBER] = sprintf(
                '2_%s',
                $row[static::TOUR_NUMBER]
            );
            $this->billingPeriodAssoc[] = $row;
        }
    }

    /**
     * @param SpyRefund $refund
     *
     * @return bool
     */
    protected function isRefundDeposit(SpyRefund $refund): bool
    {
        return ($refund->getComment() === 'Pfand');
    }

    /**
     * @param SpySalesOrder $order
     *
     * @return array
     */
    protected function getOrderInformation(SpySalesOrder $order): array
    {
        $baseInformation = [];
        $baseInformation[static::BRANCH] = $order->getSpyBranch()->getName();
        $baseInformation[static::INVOICE_NUMBER] = $order->getInvoiceReference();
        $baseInformation[static::TOUR_NUMBER] = $this->getTourReference($order);
        $baseInformation[static::EDI_NUMBER] = sprintf(
            '1_%s',
            $baseInformation[static::TOUR_NUMBER]
        );
        $baseInformation[static::SHIPMENT_DATE] = $this->getFormattedDateWithDefault(
            $this->getStartTime($order),
            static::SHIPMENT_DATE_FORMAT
        );
        $baseInformation[static::INVOICE_DATE] = $this->getFormattedDateWithDefault(
            $order->getInvoiceCreatedAt(),
            static::INVOICE_DATE_FORMAT
        );
        $baseInformation[static::ZIP_CODE] = $order->getShippingAddress()->getZipCode();
        $baseInformation[static::TIME_SLOT] = sprintf(
            static::TIME_SLOT_FORMAT,
            $this->getFormattedDateWithDefault(
                $this->getStartTime($order),
                static::TIME_SLOT_TIME_FORMAT
            ),
            $this->getFormattedDateWithDefault(
                $this->getEndTime($order),
                static::TIME_SLOT_TIME_FORMAT
            )
        );
        $baseInformation[static::ORDER_DATE] = $this->getFormattedDateWithDefault(
            $order->getCreatedAt(),
            static::ORDER_DATE_FORMAT
        );
        $baseInformation = $this->addHeidelpayShortIdsForOrder($baseInformation, $order);

        return $baseInformation;
    }

    /**
     * @param array $baseInformation
     * @param SpySalesOrder $order
     *
     * @return array
     *
     * @throws PropelException
     */
    protected function addHeidelpayShortIdsForOrder(array $baseInformation, SpySalesOrder $order): array
    {
        $criteria = (new Criteria())
            ->addAscendingOrderByColumn(DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT);

        $logEntries = $order->getDstPaymentHeidelpayRestLogs($criteria);

        $shortIds = [];

        foreach ($logEntries as $logEntry) {
            if ($logEntry->getShortId() !== null && $logEntry->getShortId() !== '') {
                $shortIds[$logEntry->getTransactionType()] = $logEntry->getShortId();
            }
        }

        for ($columnNumber = 1; $columnNumber <= 3; $columnNumber++) {
            $baseInformation[self::HEIDELPAY_SHORT_ID . ' ' . $columnNumber] = array_shift($shortIds) ?? '';
        }

        return $baseInformation;
    }

    /**
     * @param DateTime|null $dateTime
     * @param string $format
     *
     * @return string
     */
    protected function getFormattedDateWithDefault(
        ?DateTime $dateTime,
        string $format
    ): string {
        if ($dateTime === null) {
            return static::NOT_AVAILABLE;
        }

        $dateTime->setTimezone($this->config->getProjectTimeZone());

        return $dateTime->format($format);
    }

    /**
     * @param string $billingReference
     * @param int $idBranch
     *
     * @return string
     */
    protected function saveCsvFile(string $billingReference, int $idBranch): string
    {
        $filePath = $this->getFilePath($billingReference, $idBranch);
        $filePointer = fopen($filePath, 'w');
        try {
            foreach ($this->transformBillingPeriodAssocToCsvArray() as $row) {
                fputcsv(
                    $filePointer,
                    $row,
                    static::DELIMITER
                );
            }
        } finally {
            fclose($filePointer);
        }

        return $filePath;
    }

    /**
     * @param string $billingReference
     * @param int $idBranch
     *
     * @return string
     */
    protected function getFilePath(
        string $billingReference,
        int $idBranch
    ): string {
        $this->pathManager->checkZipFilePath();
        return sprintf(
            '%s/%s',
            $this->config->getBillingPeriodZipArchiveTempPath(),
            sprintf(
                static::FILE_NAME_PATTERN,
                $billingReference,
                $idBranch
            )
        );
    }

    /**
     * @return string[]
     */
    protected function getCsvHeader(): array
    {
        return [
            static::BRANCH,
            static::TYPE,
            static::INVOICE_NUMBER,
            static::TOUR_NUMBER,
            static::EDI_NUMBER,
            static::SHIPMENT_DATE,
            static::INVOICE_DATE,
            static::DURST_SKU,
            static::MERCHANT_SKU,
            static::QUANTITY,
            static::LABEL,
            static::PRICE_NET,
            static::SUM_GROSS,
            static::SUM_DISCOUNT_GROSS,
            static::TAX_RATE,
            static::ZIP_CODE,
            static::TIME_SLOT,
            static::PAYMENT_TYPE,
            static::ORDER_DATE,
            static::HEIDELPAY_SHORT_ID . ' 1',
            static::HEIDELPAY_SHORT_ID . ' 2',
            static::HEIDELPAY_SHORT_ID . ' 3',
            static::BILLING_REFERENCE,
        ];
    }

    /**
     * @param array $row
     *
     * @return array
     */
    protected function rowAssocToArray(array $row): array
    {
        $result = [];
        foreach ($this->getCsvHeader() as $column) {
            $result[] = $row[$column];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function transformBillingPeriodAssocToCsvArray(): array
    {
        $csvArray = [];
        $csvArray[] = $this->getCsvHeader();
        foreach ($this->billingPeriodAssoc as $row) {
            $csvArray[] = $this->rowAssocToArray($row);
        }

        return $csvArray;
    }

    /**
     * @param SpySalesOrderItem $orderItem
     * @return string
     * @throws PaymentMethodNotFoundException
     * @throws PropelException
     */
    protected function getPaymentMethodName(SpySalesOrderItem $orderItem): string
    {
        /** @var SpySalesPayment $payment */
        $payment = $orderItem
            ->getOrder()
            ->getOrders() // getSalesPayments() is wrongly named getOrders()
            ->getFirst();

        $paymentMethodTransfer = $this
            ->merchantFacade
            ->getPaymentMethodByCode(
                $payment
                    ->getSalesPaymentMethodType()
                    ->getPaymentMethod()
                );

        return $paymentMethodTransfer->getName();
    }
}
