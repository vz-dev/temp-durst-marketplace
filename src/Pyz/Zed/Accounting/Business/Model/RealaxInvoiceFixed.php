<?php
/**
 * Durst - project - RealaxInvoiceFixed.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.09.20
 * Time: 15:31
 */

namespace Pyz\Zed\Accounting\Business\Model;


use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\RealaxBookingHeadTransfer;
use Generated\Shared\Transfer\RealaxBookingPositionTransfer;
use Generated\Shared\Transfer\RealaxHeaderTransfer;
use Generated\Shared\Transfer\RealaxTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Pyz\Shared\Accounting\AccountingConstants;
use Pyz\Zed\Accounting\AccountingConfig;
use Pyz\Zed\Accounting\Business\Mapper\RealaxExportMapper;
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExportFixedConfigurationPlugin;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface;
use Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\Process\Process;

class RealaxInvoiceFixed implements RealaxInvoiceFixedInterface
{
    use LoggerTrait;

    protected const BILLING_MONTH = 'this month';

    protected const INVOICE_DESCRIPTION = 'Lizenzrechnung (fix) %d-%d %s';
    protected const BASIC_LICENSE_DESCRIPTION = 'Grundgebühr Lizenz %d-%d';
    protected const BASIC_MARKETING_DESCRIPTION = 'Grundgebühr Marketing %d-%d';

    protected const DOCUMENT_NUMBER = 'DE-LIF-%d-%d';

    protected const CSV_FILENAME = '%s_%s.asc';

    protected const REALAX_DATE_FORMAT = 'd.m.Y';

    protected const LICENSE_INVOICE_NOT_CREATED_ERROR = 'Es konnte keine Lizenzrechnung (fix) für den Händler "%s" erstellt werden.';

    protected const SUBJECT_LICENSE_INVOICE_MERCHANT_NO_BILLING_ADDRESS = 'Problem bei der Erstellung der Lizenzrechnung (fix)';

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface
     */
    protected $logFacade;

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface
     */
    protected $sequenceFacade;

    /**
     * @var \Pyz\Zed\Accounting\AccountingConfig
     */
    protected $config;

    /**
     * @var DateTime
     */
    protected $currentDate;

    /**
     * @var int
     */
    protected $grossInvoiceSum = 0;

    /**
     * RealaxInvoiceFixed constructor.
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface $logFacade
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface $merchantFacade
     * @param \Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface $sequenceFacade
     * @param \Pyz\Zed\Accounting\AccountingConfig $config
     */
    public function __construct(
        AccountingToLogBridgeInterface $logFacade,
        AccountingToMerchantBridgeInterface $merchantFacade,
        LicenseInvoiceReferenceGeneratorInterface $sequenceFacade,
        AccountingConfig $config
    )
    {
        $this->logFacade = $logFacade;
        $this->merchantFacade = $merchantFacade;
        $this->sequenceFacade = $sequenceFacade;
        $this->config = $config;

        $this->currentDate = new DateTime('now');
    }

    /**
     * {@inheritDoc}
     *
     * @return int[]
     */
    public function getAllMerchantsForRealaxExport(): array
    {
        $merchants = $this
            ->merchantFacade
            ->getMerchants();

        $merchantIds = [];

        foreach ($merchants as $merchant) {
            if (
                $merchant->getStatus() !== SpyMerchantTableMap::COL_STATUS_ACTIVE ||
                $merchant->getRealaxDebitor() === null ||
                strlen($merchant->getRealaxDebitor()) < 1
            ) {
                continue;
            }

            $merchantIds[] = $merchant
                ->getIdMerchant();
        }

        return $merchantIds;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException
     */
    public function getRealaxTransferByIdMerchant(int $idMerchant): RealaxTransfer
    {
        $merchantTransfer = $this
            ->merchantFacade
            ->getMerchantById($idMerchant);

        if ($merchantTransfer === null) {
            throw new MerchantNotFoundException(
                sprintf(
                    MerchantNotFoundException::MESSAGE_ID,
                    $idMerchant
                )
            );
        }

        return $this
            ->createRealaxTransfer(
                $merchantTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @param string|null $path
     * @param int|null $timeout
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     * @return int
     * @throws \Exception
     */
    public function exportRealax(
        int $idMerchant,
        ?string $path = null,
        ?int $timeout = 0,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int
    {
        if ($path === null) {
            $path = $this
                ->config
                ->getRealaxExportPath();
        }

        if ($timeout === 0) {
            $timeout = $this
                ->config
                ->getProcessTimeout();
        }

        $path = sprintf(
            '%s/%s',
            rtrim(
                $path,
                '/'
            ),
            $this
                ->generateFilename($idMerchant)
        );

        $command = 'APPLICATION_ENV=' . APPLICATION_ENV
            . ' APPLICATION_STORE=' . APPLICATION_STORE
            . ' APPLICATION_ROOT_DIR=' . APPLICATION_ROOT_DIR
            . ' APPLICATION=' . APPLICATION
            . ' ' . $this->config->getPhpPathForConsole()
            . ' vendor/bin/console middleware:process:run -p ' . RealaxExportFixedConfigurationPlugin::PROCESS_NAME
            . ' -o ' . $path
            . ' -i ' . $idMerchant;

        if (
            $applicationEnv === null ||
            $applicationStore === null ||
            $applicationRootDir === null ||
            $application === null
        ) {
            $command = $this->config->getPhpPathForConsole()
                . ' vendor/bin/console middleware:process:run -p ' . RealaxExportFixedConfigurationPlugin::PROCESS_NAME
                . ' -o ' . $path
                . ' -i ' . $idMerchant;
        }

        $process = new Process(
            $command,
            APPLICATION_ROOT_DIR,
            null,
            null,
            $timeout
        );

        $result = $process
            ->run(
                function(
                    /** @noinspection PhpUnusedParameterInspection */
                    $type,
                    $buffer
                ) {
                    echo $buffer;
                }
            );

        if (strlen(trim($process->getErrorOutput())) !== 0) {
            $message = sprintf(
                static::LICENSE_INVOICE_NOT_CREATED_ERROR,
                $idMerchant
            );

            $this
                ->getLogger()
                ->error($message);

            $this
                ->logFacade
                ->sendErrorMail(
                    static::SUBJECT_LICENSE_INVOICE_MERCHANT_NO_BILLING_ADDRESS,
                    $message
                );
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return \Generated\Shared\Transfer\RealaxTransfer
     * @throws \Exception
     */
    protected function createRealaxTransfer(MerchantTransfer $merchantTransfer): RealaxTransfer
    {
        $realaxTransfer = new RealaxTransfer();

        $realaxTransfer
            ->setHeader(
                $this
                    ->createHeaderTransfer($merchantTransfer)
            )
            ->setBookingPositions(
                $this
                    ->createBookingPositions($merchantTransfer)
            )
            ->setBookingHead(
                $this
                    ->createBookingHeadTransfer($merchantTransfer)
            );

        return $realaxTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return \Generated\Shared\Transfer\RealaxHeaderTransfer
     * @throws \Exception
     */
    protected function createHeaderTransfer(MerchantTransfer $merchantTransfer): RealaxHeaderTransfer
    {
        $headerTransfer = new RealaxHeaderTransfer();

        $billingMonth = $this
            ->getBillingMonth();

        $headerTransfer
            ->setExportType(RealaxExportMapper::HEADER_KEY)
            ->setIdentifier(AccountingConstants::REALAX_HEADER_IDENTIFIER)
            ->setNumberHandover(1)
            ->setCreatedAt(
                $this
                    ->currentDate
                    ->format(static::REALAX_DATE_FORMAT)
            )
            ->setApplicationNumber(AccountingConstants::REALAX_HEADER_APPLICATION_NUMBER)
            ->setDescription(
                sprintf(
                    static::INVOICE_DESCRIPTION,
                    $billingMonth
                        ->format('m'),
                    $billingMonth
                        ->format('y'),
                    $merchantTransfer
                        ->getCompany()
                )
            )
            ->setConvertAnsi(AccountingConstants::REALAX_HEADER_OEM_TO_ANSI)
            ->setCarryover(AccountingConstants::REALAX_HEADER_CARRYOVER);

        $this
            ->checkHeaderRequirements(
                $headerTransfer
            );

        return $headerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return \Generated\Shared\Transfer\RealaxBookingHeadTransfer
     * @throws \Exception
     */
    protected function createBookingHeadTransfer(MerchantTransfer $merchantTransfer): RealaxBookingHeadTransfer
    {
        $currentLicenseId = $this
            ->sequenceFacade
            ->getLicenseInvoiceNumberByIdMerchant($merchantTransfer->getIdMerchant());

        $currentDocumentNumber = sprintf(
            static::DOCUMENT_NUMBER,
            $currentLicenseId,
            $merchantTransfer
                ->getIdMerchant()
        );

        $billingMonth = $this
            ->getBillingMonth();

        $headTransfer = new RealaxBookingHeadTransfer();

        $headTransfer
            ->setExportType(RealaxExportMapper::BOOKING_HEAD_KEY)
            ->setIdentifier(AccountingConstants::REALAX_HEAD_IDENTIFIER)
            ->setNumberHandover(1)
            ->setPositionHandover(1)
            ->setAccountType(AccountingConstants::REALAX_HEAD_ACCOUNT_TYPE)
            ->setAccount($merchantTransfer->getRealaxDebitor())
            ->setDebits(AccountingConstants::REALAX_HEAD_DEBITS)
            ->setDocumentNumber($currentDocumentNumber)
            ->setForeignDocumentNumber($currentDocumentNumber)
            ->setDocumentDate(
                $this
                    ->currentDate
                    ->format(static::REALAX_DATE_FORMAT)
            )
            ->setBookingTypeNumber(AccountingConstants::REALAX_HEAD_BOOKING_TYPE_NUMBER)
            ->setBookingText(
                sprintf(
                    static::INVOICE_DESCRIPTION,
                    $billingMonth
                        ->format('m'),
                    $billingMonth
                        ->format('y'),
                    $merchantTransfer
                        ->getCompany()
                )
            )
            ->setCurrency(AccountingConstants::REALAX_HEAD_CURRENCY)
            ->setAmount(
                $this
                    ->getRealaxMoney(
                        $this
                            ->grossInvoiceSum
                    )
            )
            ->setPaymentType(AccountingConstants::REALAX_HEAD_PAYMENT_TYPE)
            ->setPaymentCondition(AccountingConstants::REALAX_HEAD_PAYMENT_CONDITION)
            ->setAutoDunning(AccountingConstants::REALAX_HEAD_AUTO_DUNNING)
            ->setAutoRegulation(AccountingConstants::REALAX_HEAD_AUTO_REGULATION)
            ->setBaseCurrency(AccountingConstants::REALAX_HEAD_CURRENCY);

        $this
            ->checkBookingHeadRequirements(
                $headTransfer
            );

        return $headTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return \Generated\Shared\Transfer\RealaxBookingPositionTransfer[]|ArrayObject
     * @throws \Exception
     */
    protected function createBookingPositions(MerchantTransfer $merchantTransfer): ArrayObject
    {
        $this->grossInvoiceSum = 0;

        $billingMonth = $this
            ->getBillingMonth();

        $amountLicenseFixed = (float)($merchantTransfer->getLicenseFixed() / 100);
        $grossAmount = (
            $amountLicenseFixed *
            $this
                ->getTaxRate(
                    $billingMonth
                )
        );
        $taxAmount = ($grossAmount - $amountLicenseFixed);

        $this->grossInvoiceSum += $grossAmount;

        $billingPosition = 1;

        $positions = new ArrayObject();

        $basicCharge = new RealaxBookingPositionTransfer();

        $basicCharge
            ->setExportType(RealaxExportMapper::BOOKING_POS_KEY)
            ->setIdentifier(AccountingConstants::REALAX_POSITION_IDENTIFIER)
            ->setNumberHandover(1)
            ->setPositionHandover(1)
            ->setOffsetAccount($billingPosition)
            ->setAccountType(AccountingConstants::REALAX_POSITION_ACCOUNT_TYPE)
            ->setAccount(
                $this
                    ->getLicenseFixed(
                        $billingMonth
                    )
            )
            ->setDebits(AccountingConstants::REALAX_POSITION_DEBITS)
            ->setBookingText(
                sprintf(
                    static::BASIC_LICENSE_DESCRIPTION,
                    $billingMonth
                        ->format('m'),
                    $billingMonth
                        ->format('y')
                )
            )
            ->setTaxType(AccountingConstants::REALAX_POSITION_TAX_TYPE)
            ->setCustomsDutyKey(
                $this
                    ->getCustomsDutyKey(
                        $billingMonth
                    )
            )
            ->setAmountNet(
                $this
                    ->getRealaxMoney($amountLicenseFixed)
            )
            ->setTaxAmount(
                $this
                    ->getRealaxMoney($taxAmount)
            )
            ->setQuantity(
                1
            );


        $this
            ->checkBookingPositionRequirements(
                $basicCharge
            );

        $positions
            ->append($basicCharge);


        $amountMarketingFixed = (float)($merchantTransfer->getMarketingFixed() / 100);
        $grossMarketingAmount = (
            $amountMarketingFixed *
            $this
                ->getTaxRate(
                    $billingMonth
                )
        );
        $taxMarketingAmount = ($grossMarketingAmount - $amountMarketingFixed);

        $this->grossInvoiceSum += $grossMarketingAmount;

        $billingPosition++;

        $marketingCharge = new RealaxBookingPositionTransfer();

        $marketingCharge
            ->setExportType(RealaxExportMapper::BOOKING_POS_KEY)
            ->setIdentifier(AccountingConstants::REALAX_POSITION_IDENTIFIER)
            ->setNumberHandover(1)
            ->setPositionHandover(1)
            ->setOffsetAccount($billingPosition)
            ->setAccountType(AccountingConstants::REALAX_POSITION_ACCOUNT_TYPE)
            ->setAccount(
                $this
                    ->getMarketingFixed(
                        $billingMonth
                    )
            )
            ->setDebits(AccountingConstants::REALAX_POSITION_DEBITS)
            ->setBookingText(
                sprintf(
                    static::BASIC_MARKETING_DESCRIPTION,
                    $billingMonth
                        ->format('m'),
                    $billingMonth
                        ->format('y')
                )
            )
            ->setTaxType(AccountingConstants::REALAX_POSITION_TAX_TYPE)
            ->setCustomsDutyKey(
                $this
                    ->getCustomsDutyKey(
                        $billingMonth
                    )
            )
            ->setAmountNet(
                $this
                    ->getRealaxMoney($amountMarketingFixed)
            )
            ->setTaxAmount(
                $this
                    ->getRealaxMoney($taxMarketingAmount)
            )
            ->setQuantity(
                1
            );

        $this
            ->checkBookingPositionRequirements(
                $marketingCharge
            );

        $positions
            ->append($marketingCharge);

        return $positions;
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    protected function getBillingMonth(): DateTime
    {
        return (new DateTime(static::BILLING_MONTH))
            ->setTime(
                0,
                0,
                0
            );
    }

    /**
     * @param int $idMerchant
     * @return string
     * @throws \Exception
     */
    protected function generateFilename(int $idMerchant): string
    {
        $licenseNumber = sprintf(
            static::DOCUMENT_NUMBER,
            $this
                ->sequenceFacade
                ->generateLicenseInvoiceNumber(
                    $idMerchant
                ),
            $idMerchant
        );

        return sprintf(
            static::CSV_FILENAME,
            $licenseNumber,
            $this
                ->currentDate
                ->format('U')
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RealaxHeaderTransfer $headerTransfer
     * @return void
     */
    protected function checkHeaderRequirements(RealaxHeaderTransfer $headerTransfer): void
    {
        $headerTransfer
            ->requireIdentifier()
            ->requireNumberHandover()
            ->requireCreatedAt()
            ->requireConvertAnsi()
            ->requireCarryover();
    }

    /**
     * @param \Generated\Shared\Transfer\RealaxBookingHeadTransfer $bookingHeadTransfer
     * @return void
     */
    protected function checkBookingHeadRequirements(RealaxBookingHeadTransfer $bookingHeadTransfer): void
    {
        $bookingHeadTransfer
            ->requireIdentifier()
            ->requireNumberHandover()
            ->requirePositionHandover()
            ->requireAccountType()
            ->requireAccount()
            ->requireDebits()
            ->requireDocumentNumber()
            ->requireDocumentDate()
            ->requireBookingTypeNumber()
            ->requireCurrency()
            ->requireAmount()
            ->requirePaymentType()
            ->requirePaymentCondition()
            ->requireAutoDunning()
            ->requireAutoRegulation()
            ->requireBaseCurrency();
    }

    /**
     * @param \Generated\Shared\Transfer\RealaxBookingPositionTransfer $bookingPositionTransfer
     * @return void
     */
    protected function checkBookingPositionRequirements(RealaxBookingPositionTransfer $bookingPositionTransfer): void
    {
        $bookingPositionTransfer
            ->requireIdentifier()
            ->requireNumberHandover()
            ->requirePositionHandover()
            ->requireOffsetAccount()
            ->requireAccountType()
            ->requireAccount()
            ->requireDebits()
            ->requireTaxType()
            ->requireCustomsDutyKey()
            ->requireAmountNet()
            ->requireTaxAmount();
    }

    /**
     * @param float $amount
     * @return string
     */
    protected function getRealaxMoney(float $amount): string
    {
        return number_format(
            $amount,
            2,
            ',',
            ''
        );
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    protected function isCoronaReducedTaxPeriod(
        DateTime $dateTime
    ): bool
    {
        if (
            in_array((int)$dateTime->format('m'), $this->config->getCoronaTaxReductionMonth()) &&
            in_array((int)$dateTime->format('Y'), $this->config->getCoronaTaxReductionYear())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param \DateTime $invoiceDate
     * @return float
     */
    protected function getTaxRate(
        DateTime $invoiceDate
    ): float
    {
        if ($this->isCoronaReducedTaxPeriod($invoiceDate) === true) {
            return $this
                ->config
                ->getRealaxTaxRateCorona();
        }

        return $this
            ->config
            ->getRealaxTaxRateNormal();
    }

    /**
     * @param \DateTime $invoiceDate
     * @return string
     */
    protected function getLicenseFixed(
        DateTime $invoiceDate
    ): string
    {
        if ($this->isCoronaReducedTaxPeriod($invoiceDate) === true) {
            return $this
                ->config
                ->getLicenseInvoiceFixedReducedKey();
        }

        return $this
            ->config
            ->getLicenseInvoiceFixedKey();
    }

    /**
     * @param \DateTime $invoiceDate
     * @return string
     */
    protected function getMarketingFixed(
        DateTime $invoiceDate
    ): string
    {
        if ($this->isCoronaReducedTaxPeriod($invoiceDate) === true) {
            return $this
                ->config
                ->getMarketingInvoiceFixedReducedKey();
        }

        return $this
            ->config
            ->getMarketingInvoiceFixedKey();
    }

    /**
     * @param \DateTime $invoiceDate
     * @return int
     */
    protected function getCustomsDutyKey(
        DateTime $invoiceDate
    ): int
    {
        if ($this->isCoronaReducedTaxPeriod($invoiceDate) === true) {
            return AccountingConstants::REALAX_POSITION_CUSTOMS_DUTY_KEY_REDUCED;
        }

        return AccountingConstants::REALAX_POSITION_CUSTOMS_DUTY_KEY;
    }
}
