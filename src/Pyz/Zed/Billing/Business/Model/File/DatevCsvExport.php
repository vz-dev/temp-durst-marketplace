<?php
/**
 * Durst - project - DatevCsvExport.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.07.20
 * Time: 13:58
 */

namespace Pyz\Zed\Billing\Business\Model\File;


use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Orm\Zed\Billing\Persistence\DstBillingItem;
use Orm\Zed\Billing\Persistence\DstBillingPeriod;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\Billing\Business\Exception\CouldNotCreateDatevCsvException;
use Pyz\Zed\Billing\Business\Exception\DirectoryCouldNotBeCreatedException;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMoneyBridgeInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException;
use SplFileObject;

class DatevCsvExport extends AbstractCsvExportManager implements DatevCsvExportInterface
{
    protected const FILE_NAME_PATTERN = 'datev_billing_%s_%d.csv';
    protected const DELIMITER = ';';

    protected const BILLING_REFERENCE = 'Sammelbelegs-ID';
    protected const TOUR_NUMBER = 'Tournr.';
    protected const INVOICE_NUMBER = 'Rechnungsnr.';
    protected const SHIPMENT_DATE = 'Lieferdatum';
    protected const SHIPMENT_DATE_FORMAT = 'd.m.y';
    protected const INVOICE_DATE = 'Rechnungsdatum';
    protected const INVOICE_DATE_FORMAT = 'd.m.y';
    protected const SUM_GROSS = 'Gesamtpreis Brutto';
    protected const PAYMENT_TYPE = 'Zahlart';
    protected const HEIDELPAY_SHORT_ID = 'Heidelpay Short-ID';
    protected const ACCOUNT = 'Konto';
    protected const CONTRA_ACCOUNT = 'Gegenkonto';
    protected const BOOKING_TEXT = 'Buchungstext';
    protected const BOOKING_TEXT_FORMAT = '%s, %s - %s';

    protected const PAYMENT_TYPE_UNKNOWN = 'unknown';

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
     * @var BillingToMoneyBridgeInterface
     */
    protected $moneyFacade;

    /**
     * @var BillingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphmastersFacade;

    /**
     * @var string[]
     */
    protected $csvHeaders = [];

    /**
     * @var array
     */
    protected $csvRows = [];

    /**
     * @var DstBillingPeriod
     */
    protected $billingPeriodEntity;

    /**
     * DatevCsvExport constructor.
     * @param BillingQueryContainerInterface $queryContainer
     * @param BillingConfig $config
     * @param BillingToMoneyBridgeInterface $moneyFacade
     * @param BillingToMerchantBridgeInterface $merchantFacade
     */
    public function __construct(
        BillingQueryContainerInterface $queryContainer,
        BillingConfig $config,
        BillingToMoneyBridgeInterface $moneyFacade,
        BillingToMerchantBridgeInterface $merchantFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->moneyFacade = $moneyFacade;
        $this->merchantFacade = $merchantFacade;
        $this->graphmastersFacade = $graphMastersFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param BillingPeriodTransfer $billingPeriodTransfer
     * @return string
     * @throws PropelException
     */
    public function createDatevCsvForBillingPeriod(BillingPeriodTransfer $billingPeriodTransfer): string
    {
        try {
            $this->billingPeriodEntity = $this
                ->getEntityEagerLoaded(
                    $billingPeriodTransfer
                        ->getIdBillingPeriod()
                );

            $this
                ->prepareCsvAssocArray();

        } catch (BillingPeriodEntityNotFoundException $e) {
            // if any information is missing we just save a csv file with the header
        }

        $this
            ->prepareHeaders();

        return $this
            ->saveCsvFile(
                $billingPeriodTransfer
                    ->getBillingReference(),
                $billingPeriodTransfer
                    ->getBranch()
                    ->getIdBranch()
            );
    }

    /**
     * @param int $idBillingPeriod
     * @return DstBillingPeriod
     * @throws BillingPeriodEntityNotFoundException
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
     * @return void
     * @throws PropelException
     */
    protected function prepareCsvAssocArray(): void
    {
        foreach ($this->billingPeriodEntity->getDstBillingItems() as $dstBillingItem) {
            $order = $dstBillingItem
                ->getSpySalesOrder();

            $this->csvRows[] = [
                static::BILLING_REFERENCE => $this
                    ->billingPeriodEntity
                    ->getBillingReference(),
                static::TOUR_NUMBER => $this->getTourReference($order),
                static::INVOICE_NUMBER => $order
                    ->getInvoiceReference(),
                static::SHIPMENT_DATE => $this
                    ->getFormattedDateWithDefault(
                        $this->getStartTime($order),
                        static::SHIPMENT_DATE_FORMAT
                    ),
                static::INVOICE_DATE => $this
                    ->getFormattedDateWithDefault(
                        $order
                            ->getInvoiceCreatedAt(),
                        static::INVOICE_DATE_FORMAT
                    ),
                static::SUM_GROSS => $this
                    ->formatMoney(
                        $dstBillingItem
                            ->getAmount()
                    ),
                static::PAYMENT_TYPE => $this
                    ->mapPaymentType($order),
                static::HEIDELPAY_SHORT_ID => $this
                    ->getHeidelpayShortIdForOrder($order),
                static::ACCOUNT => $order
                    ->getSpyBranch()
                    ->getExportAccount(),
                static::CONTRA_ACCOUNT => $order
                    ->getSpyBranch()
                    ->getExportContraAccount(),
                static::BOOKING_TEXT => $this
                    ->getBookingText(
                        $dstBillingItem
                    )
            ];
        }
    }

    /**
     * @return void
     */
    protected function prepareHeaders(): void
    {
        $this->csvHeaders = [
            static::BILLING_REFERENCE,
            static::TOUR_NUMBER,
            static::INVOICE_NUMBER,
            static::SHIPMENT_DATE,
            static::INVOICE_DATE,
            static::SUM_GROSS,
            static::PAYMENT_TYPE,
            static::HEIDELPAY_SHORT_ID,
            static::ACCOUNT,
            static::CONTRA_ACCOUNT,
            static::BOOKING_TEXT
        ];
    }

    /**
     * @param array $row
     * @return array
     */
    protected function rowAssocToArray(array $row): array
    {
        $result = [];
        foreach ($this->csvHeaders as $column) {
            $result[] = $row[$column];
        }

        return $result;
    }

    /**
     * @param string $billingReference
     * @param int $idBranch
     * @return string
     * @throws CouldNotCreateDatevCsvException
     */
    protected function saveCsvFile(
        string $billingReference,
        int $idBranch
    ): string
    {
        $filePath = $this
            ->getFilePath(
                $billingReference,
                $idBranch
            );

        $this->createDirectoryIfNecessary($filePath);

        $fileHandle = new SplFileObject(
            $filePath,
            'w'
        );

        try {
            $fileHandle
                ->fputcsv(
                    $this->csvHeaders,
                    static::DELIMITER
                );

            foreach ($this->csvRows as $csvRow) {
                $row = $this
                    ->rowAssocToArray($csvRow);

                $fileHandle
                    ->fputcsv(
                        $row,
                        static::DELIMITER
                    );
            }

        } finally {
            $writeResult = $fileHandle
                ->fflush();
        }

        if ($writeResult !== true) {
            throw CouldNotCreateDatevCsvException::build(
                $fileHandle
                    ->getFilename(),
                $fileHandle
                    ->getPath()
            );
        }

        return $filePath;
    }

    /**
     * @param string $billingReference
     * @param int $idBranch
     * @return string
     */
    protected function getFilePath(
        string $billingReference,
        int $idBranch
    ): string
    {
        return sprintf(
            '%s/%s',
            $this
                ->config
                ->getBillingPeriodZipArchiveTempPath(),
            sprintf(
                static::FILE_NAME_PATTERN,
                $billingReference,
                $idBranch
            )
        );
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

        return $dateTime
            ->format($format);
    }

    /**
     * @param string|null $value
     * @return string
     */
    protected function getValueWithDefault(
        ?string $value
    ): string
    {
        if (
            $value === null ||
            empty($value) === true
        ) {
            return static::NOT_AVAILABLE;
        }

        return $value;
    }

    /**
     * @param int $amount
     * @return string
     */
    protected function formatMoney(int $amount): string
    {
        $moneyTransfer = $this
            ->moneyFacade
            ->fromInteger(
                $amount
            );

        return $this
            ->moneyFacade
            ->formatWithSymbol(
                $moneyTransfer
            );
    }

    /**
     * @param SpySalesOrder $order
     * @return string
     * @throws PropelException
     */
    protected function mapPaymentType(SpySalesOrder $order): string
    {
        $processName = $order
            ->getItems()
            ->getFirst()
            ->getProcess()
            ->getName();

        $paymentTypeMap = $this->config->getCsvExportPaymentTypeMap();

        if (array_key_exists($processName, $paymentTypeMap) === true) {
            return $paymentTypeMap[$processName];
        }

        try {
            return $this->getPaymentMethodName($order);
        } catch (PaymentMethodNotFoundException $exception) {
            return static::PAYMENT_TYPE_UNKNOWN;
        }
    }

    /**
     * @param DstBillingItem $billingItem
     * @return string
     * @throws PropelException
     */
    protected function getBookingText(DstBillingItem $billingItem): string
    {
        $shippingAddress = $billingItem
            ->getSpySalesOrder()
            ->getShippingAddress();

        return sprintf(
            static::BOOKING_TEXT_FORMAT,
            $shippingAddress
                ->getLastName(),
            $shippingAddress
                ->getFirstName(),
            $this->mapPaymentType(
                $billingItem->getSpySalesOrder()
            )
        );
    }

    /**
     * @param string $filePath
     * @throws DirectoryCouldNotBeCreatedException
     */
    protected function createDirectoryIfNecessary(string $filePath): void
    {
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);

        if (is_dir($dir) !== true) {
            $result = mkdir($dir, 0755, true);

            if ($result !== true) {
                throw new DirectoryCouldNotBeCreatedException(
                    sprintf(
                        DirectoryCouldNotBeCreatedException::MESSAGE,
                        $dir
                    )
                );
            }
        }
    }

    /**
     * @param SpySalesOrder $order
     * @return string
     * @throws PaymentMethodNotFoundException
     * @throws PropelException
     */
    protected function getPaymentMethodName(SpySalesOrder $order): string
    {
        /** @var SpySalesPayment $payment */
        $payment = $order
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

    /**
     * @param SpySalesOrder $order
     * @return string
     * @throws PropelException
     */
    protected function getHeidelpayShortIdForOrder(SpySalesOrder $order): string
    {
        foreach ($order->getDstPaymentHeidelpayRestLogs() as $logEntry) {
            if ($logEntry->getShortId() !== null && $logEntry->getShortId() !== '') {
                return $logEntry->getShortId();
            }
        }

        return '';
    }
}
