<?php
/**
 * Durst - merchant_center - InvoiceMailManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.08.18
 * Time: 15:05
 */

namespace Pyz\Zed\Oms\Business\Model\Mail;

use DateTime;
use Exception;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailAttachmentTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PdfTransfer;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManagerInterface;
use Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface;
use Pyz\Zed\Oms\OmsConfig;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Oms\Dependency\Facade\OmsToMailInterface;

class InvoiceMailManager implements InvoiceMailManagerInterface
{
    use LoggerTrait;

    public const SURVEY_DATE_FORMAT = 'dmy';

    protected const PDF_CREATION_ERROR = 'Ein PDF für die Bestellung %s konnte nicht erstellt werden: %s';

    public const RETURN_DEPOSIT_UNITS_LABEL_MAP = [
        'bottles' => 'Flasche(n)',
        'cases' => 'Rahmen',
        'deposit' => 'Gebinde'
    ];
    public const REFUND_EXPENSE_DEPOSIT_PREFIX = 'deposit-';
    public const REFUND_EXPENSE_TYPE = 'REFUND_EXPENSE';
    public const DELIVERY_COST_EXPENSE_TYPE = 'DELIVERY_COST_EXPENSE_TYPE';
    public const REFUND_DEPOSIT_TYPE_SEPERATOR = '_';
    public const RETURNED_DEPOSIT_TYPE = 'RETURNED_DEPOSIT_TYPE';
    public const REFUND_DEPOSIT_ID_REPLACEMENT = '/(_CASES|_DEPOSIT|_BOTTLES)$/';
    public const REFUND_DEPOSIT_NAME_KEY = 'name';
    public const REFUND_DEPOSIT_NAME_REPLACEMENTS = ['Pfandrückgabe - ', ' - Gebinde', ' - Rahmen', ' - einzelne Flasche(n)'];
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_DEPOSITS = 'deposits';
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_RETURN_DEPOSITS = 'returnDeposits';
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_DELIVERY_FEES = 'deliveryFees';
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUND_EXPENSES = 'refundExpenses';
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUND_DEPOSITS = 'refundDeposits';
    public const SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUNDS = 'refunds';


    /**
     * @var OmsToMailInterface
     */
    protected $mailFacade;

    /**
     * @var OmsConfig
     */
    protected $config;

    /**
     * @var DurstCompanyDetailsManagerInterface
     */
    protected $durstCompanyDetailsManager;

    /**
     * @var HeidelpayRestFacadeInterface
     */
    protected $heidelpayRestFacade;

    /**
     * @var OmsToInvoiceBridgeInterface
     */
    protected $invoiceFacade;

    /**
     * @var TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var TermsOfServiceFacadeInterface
     */
    protected $termsOfServiceFacade;

    /**
     * InvoiceMailManager constructor.
     * @param OmsToMailInterface $mailFacade
     * @param OmsConfig $config
     * @param DurstCompanyDetailsManagerInterface $durstCompanyDetailsManager
     * @param HeidelpayRestFacadeInterface $heidelpayRestFacade
     * @param OmsToInvoiceBridgeInterface $invoiceBridge
     * @param TaxFacadeInterface $taxFacade
     * @param TermsOfServiceFacadeInterface $termsOfServiceFacade
     */
    public function __construct(
        OmsToMailInterface $mailFacade,
        OmsConfig $config,
        DurstCompanyDetailsManagerInterface $durstCompanyDetailsManager,
        HeidelpayRestFacadeInterface $heidelpayRestFacade,
        OmsToInvoiceBridgeInterface $invoiceBridge,
        TaxFacadeInterface $taxFacade,
        TermsOfServiceFacadeInterface $termsOfServiceFacade
    ) {
        $this->mailFacade = $mailFacade;
        $this->config = $config;
        $this->durstCompanyDetailsManager = $durstCompanyDetailsManager;
        $this->heidelpayRestFacade = $heidelpayRestFacade;
        $this->invoiceFacade = $invoiceBridge;
        $this->taxFacade = $taxFacade;
        $this->termsOfServiceFacade = $termsOfServiceFacade;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param BranchTransfer $branchTransfer
     * @param string $mailType
     * @param MailTransfer|null $mailTransfer
     *
     * @return void
     */
    public function sendMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, string $mailType, ?MailTransfer $mailTransfer = null)
    {
        $termsOfService = $branchTransfer->getTermsOfService();
        $termsOfServiceLines = explode(PHP_EOL, $termsOfService);

        $branchTransfer->setTermsOfService($termsOfService);
        $branchTransfer->setTermsOfServiceLines($termsOfServiceLines);

        if ($mailTransfer === null) {
            $mailTransfer = $this
                ->createMailTransfer();
        }

        $mailTransfer
            ->setType($mailType)
            ->setOrder($orderTransfer)
            ->setSplitExpensesRefundsReturnDeposits($this->getSplitExpensesRefundsReturnDepositsFromOrder($orderTransfer))
            ->setTaxRate($this->getTaxRateFromOrderItems($orderTransfer))
            ->setTermsOfService($this->getTermsOfService($orderTransfer->getCreatedAt()))
            ->setBranch($branchTransfer)
            ->setBaseUrl($this->config->getBaseUrl())
            ->setPdfAssetsPath($this->config->getPdfAssetPath())
            ->setDurst($this->durstCompanyDetailsManager->createDurstCompanyTransfer())
            ->setSurveyUrls($this->buildSurveyUrls($orderTransfer, $branchTransfer))
            ->setShortId($this->getShortIdByIdSalesOrder($orderTransfer->getIdSalesOrder()));

        if ($orderTransfer->getDurstCustomerReference() !== null) {
            $mailTransfer->setDurstCustomerReference($orderTransfer->getDurstCustomerReference());
        }

        $mailAttachment = $this
            ->getInvoicePdfFromMailTransfer($mailTransfer);

        if ($mailAttachment !== null) {
            $mailTransfer
                ->addAttachment($mailAttachment);
        }

        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }

    /**
     * @return MailTransfer
     */
    protected function createMailTransfer() : MailTransfer
    {
        return new MailTransfer();
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param BranchTransfer $branchTransfer
     *
     * @return array
     */
    protected function buildSurveyUrls(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer) : array
    {
        $surveyUrls = [];

        $happinessTypes = $this->config->getSurveyHappinessTypes();

        foreach ($happinessTypes as $happyType) {
            $surveyUrls[$happyType] = sprintf(
                $this->config->getSurveyUrl(),
                $happyType,
                $orderTransfer->getShippingAddress()->getZipCode(),
                $branchTransfer->getIdBranch(),
                $this->getDeliveryDateString($orderTransfer),
                $orderTransfer->getTotals()->getGrandTotal()
            );
        }

        return $surveyUrls;
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return string
     */
    protected function getDeliveryDateString(OrderTransfer $orderTransfer) : string
    {
        if($orderTransfer->getGmStartTime() !== null){
            $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $orderTransfer->getGmStartTime());
            return $date->format(static::SURVEY_DATE_FORMAT);
        }

        $date = DateTime::createFromFormat(DateTime::RFC3339, $orderTransfer->getConcreteTimeSlot()->getStartTime());
        return $date->format(static::SURVEY_DATE_FORMAT);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return string|null
     */
    protected function getShortIdByIdSalesOrder(int $idSalesOrder): ?string
    {
        $logTransfer = $this
            ->heidelpayRestFacade
            ->getHeidelpayRestLogByIdSalesOrderAndTransactionType(
                $idSalesOrder,
                HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
            );

        if ($logTransfer === null) {
            return null;
        }

        return $logTransfer
            ->getShortId();
    }

    /**
     * @param MailTransfer $mailTransfer
     *
     * @return MailAttachmentTransfer|null
     */
    protected function getInvoicePdfFromMailTransfer(MailTransfer $mailTransfer): ?MailAttachmentTransfer
    {
        try {
            $pdfTransfer = $this
                ->invoiceFacade
                ->createInvoicePdfFromMailTransfer($mailTransfer);

            if ($pdfTransfer->getFileName() !== null) {
                return $this
                    ->createMailAttachment($pdfTransfer);
            }
        } catch (Exception $exception) {
            $this
                ->getLogger()
                ->error(
                    sprintf(
                        static::PDF_CREATION_ERROR,
                        $mailTransfer->getOrder()->getOrderReference(),
                        $exception->getMessage()
                    )
                );
        }

        return null;
    }

    /**
     * @param PdfTransfer $pdfTransfer
     *
     * @return MailAttachmentTransfer
     */
    protected function createMailAttachment(PdfTransfer $pdfTransfer): MailAttachmentTransfer
    {
        return (new MailAttachmentTransfer())
            ->setAttachmentUrl($pdfTransfer->getFileName())
            ->setFileName($pdfTransfer->getPdfName())
            ->setDisplayName($pdfTransfer->getDisplayName());
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return string
     */
    protected function getTaxRateFromOrderItems(OrderTransfer $orderTransfer) : string
    {
        if($orderTransfer->getGmStartTime() !== null){
            $deliveryDate = DateTime::createFromFormat('Y-m-d H:i:s.u', $orderTransfer->getGmStartTime());
        }else{
            $deliveryDate = DateTime::createFromFormat(DateTime::ISO8601, $orderTransfer->getConcreteTimeSlot()->getStartTime());
        }

        return sprintf('%g', $this->taxFacade->getDefaultTaxRateForDate($deliveryDate));
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function getSplitExpensesRefundsReturnDepositsFromOrder(OrderTransfer $orderTransfer)  : array
    {
        $depositsArray = [];
        $returnDepositsArray = [];
        $deliveryFeeArray = [];
        $refundExpenseArray = [];

        $refundDepositsArray = [];
        $refundArray = [];

        foreach ($orderTransfer->getRefunds() as $refund) {
            if ($this->getIsRefundDepositType($refund->getSku())) {
                $refundDepositsArray[] = $refund;
                continue;
            }

            $refundArray[] = $refund;
        }

        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($this->getIsReturnDepositType($expense->getType())) {
                $refundDepositId = preg_replace(self::REFUND_DEPOSIT_ID_REPLACEMENT, '', $expense->getType());

                if (array_key_exists($refundDepositId, $returnDepositsArray) !== true) {
                    $returnDepositsArray[$refundDepositId] = [];
                    $returnDepositsArray[$refundDepositId][self::REFUND_DEPOSIT_NAME_KEY] = str_replace(self::REFUND_DEPOSIT_NAME_REPLACEMENTS, '', $expense->getName());
                }

                $depositType = strtolower(str_replace($refundDepositId . self::REFUND_DEPOSIT_TYPE_SEPERATOR, '', $expense->getType()));
                $returnDepositsArray[$refundDepositId][$depositType] = $expense->setName(self::RETURN_DEPOSIT_UNITS_LABEL_MAP[$depositType]);

                continue;
            }

            if ($this->getIsDeliveryCostExpenseType($expense->getType(), $expense->getSumPrice())){
                $deliveryFeeArray[] = $expense;
                continue;
            }

            if($this->getIsRefundExpenseType($expense->getType()))
            {
                $refundExpenseArray[] = $expense;
                continue;
            }

            if($this->getDepositExpenseType($expense->getType(), $expense->getSumPrice())){
                $depositsArray[] = $expense;
            }
        }

        return [
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_DEPOSITS => $depositsArray,
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_RETURN_DEPOSITS => $returnDepositsArray,
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_DELIVERY_FEES => $deliveryFeeArray,
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUND_EXPENSES => $refundExpenseArray,
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUND_DEPOSITS => $refundDepositsArray,
            self::SPLIT_DEPOSITS_REFUNDS_EXPENSES_KEY_REFUNDS => $refundArray,
        ];
    }

    /**
     * @param string $createdAt
     * @return string
     */
    protected function getTermsOfService(string $createdAt) : string
    {
        $timestamp = strtotime($createdAt);
        return $this->termsOfServiceFacade->getActiveCustomerTermsByTimestamp($timestamp)->getText();
    }

    /**
     * @param string $sku
     * @return bool
     */
    protected function getIsRefundDepositType(string $sku) : bool
    {
        return (strncmp($sku, self::REFUND_EXPENSE_DEPOSIT_PREFIX, strlen(self::REFUND_EXPENSE_DEPOSIT_PREFIX)) === 0);
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function getIsReturnDepositType(string $type) : bool
    {
        return (strncmp($type, self::RETURNED_DEPOSIT_TYPE, strlen(self::RETURNED_DEPOSIT_TYPE)) === 0);
    }

    /**
     * @param string $type
     * @param int $sumPrice
     * @return bool
     */
    protected function getIsDeliveryCostExpenseType(string $type, int $sumPrice) : bool
    {
        return (strncmp($type, self::DELIVERY_COST_EXPENSE_TYPE, strlen(self::DELIVERY_COST_EXPENSE_TYPE)) === 0 && $sumPrice > 0);
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function getIsRefundExpenseType(string $type) : bool
    {
       return (strncmp($type, self::REFUND_EXPENSE_TYPE, strlen(self::REFUND_EXPENSE_TYPE)) === 0);
    }

    /**
     * @param string $type
     * @param int $sumPrice
     * @return bool
     */
    protected function getDepositExpenseType(string $type, int $sumPrice) : bool
    {
        return (strncmp($type, self::REFUND_EXPENSE_DEPOSIT_PREFIX, strlen(self::REFUND_EXPENSE_DEPOSIT_PREFIX)) === 0 && $sumPrice > 0);
    }
}
