<?php
/**
 * Durst - project - RealaxInvoice.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.20
 * Time: 17:41
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
use Pyz\Zed\Accounting\Communication\Plugin\RealaxExportConfigurationPlugin;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridgeInterface;
use Pyz\Zed\Merchant\Business\Exception\MerchantNotFoundException;
use Spryker\Shared\Log\LoggerTrait;
use Symfony\Component\Process\Process;

class RealaxInvoice implements RealaxInvoiceInterface
{
    use LoggerTrait;

    protected const FIRST_DAY_OF_BILLING_MONTH = 'first day of last month';
    protected const LAST_DAY_OF_BILLING_MONTH = 'last day of last month';

    protected const INVOICE_DESCRIPTION = 'Lizenzrechnung (variabel) %d-%d %s';
    protected const BRANCH_CHARGE_DESCRIPTION = 'Transaktionskosten %d-%d für %s';

    protected const DOCUMENT_NUMBER = 'DE-LIV-%d-%d';

    protected const CSV_FILENAME = '%s_%s.asc';

    protected const REALAX_DATE_FORMAT = 'd.m.Y';

    protected const LICENSE_INVOICE_NOT_CREATED_ERROR = 'Es konnte keine Lizenzrechnung (variabel) für den Händler "%s" erstellt werden.';

    protected const SUBJECT_LICENSE_INVOICE_MERCHANT_NO_BILLING_ADDRESS = 'Problem bei der Erstellung der Lizenzrechnung (variabel)';

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface
     */
    protected $logFacade;

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridgeInterface
     */
    protected $salesFacade;

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
     * RealaxInvoice constructor.
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface $logFacade
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface $merchantFacade
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridgeInterface $salesFacade
     * @param \Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface $sequenceFacade
     * @param \Pyz\Zed\Accounting\AccountingConfig $config
     * @throws \Exception
     */
    public function __construct(
        AccountingToLogBridgeInterface $logFacade,
        AccountingToMerchantBridgeInterface $merchantFacade,
        AccountingToSalesBridgeInterface $salesFacade,
        LicenseInvoiceReferenceGeneratorInterface $sequenceFacade,
        AccountingConfig $config
    )
    {
        $this->logFacade = $logFacade;
        $this->merchantFacade = $merchantFacade;
        $this->salesFacade = $salesFacade;
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
            . ' vendor/bin/console middleware:process:run -p ' . RealaxExportConfigurationPlugin::PROCESS_NAME
            . ' -o ' . $path
            . ' -i ' . $idMerchant;

        if (
            $applicationEnv === null ||
            $applicationStore === null ||
            $applicationRootDir === null ||
            $application === null
        ) {
            $command = $this->config->getPhpPathForConsole()
                . ' vendor/bin/console middleware:process:run -p ' . RealaxExportConfigurationPlugin::PROCESS_NAME
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
            ->getFirstOfBillingMonth();

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
            ->getFirstOfBillingMonth();

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
            ->getFirstOfBillingMonth();

        $billingPosition = 0;

        $positions = new ArrayObject();

        $branches = $this
            ->merchantFacade
            ->getBranchesByIdMerchant(
                $merchantTransfer
                    ->getIdMerchant()
            );

        foreach ($branches as $branch) {
            $billingPosition++;

            $quantity = $this
                ->getSalesOrderCountByIdBranch(
                    $branch
                        ->getIdBranch()
                );

            $branchAmount = (($quantity * $merchantTransfer->getLicenseVariable()) / 100);

            $grossBranchAmount = (
                $branchAmount *
                $this
                    ->getTaxRate(
                        $billingMonth
                    )
            );
            $taxBranchAmount = ($grossBranchAmount - $branchAmount);

            $this->grossInvoiceSum += $grossBranchAmount;

            $branchCharge = new RealaxBookingPositionTransfer();
            $branchCharge
                ->setExportType(RealaxExportMapper::BOOKING_POS_KEY)
                ->setIdentifier(AccountingConstants::REALAX_POSITION_IDENTIFIER)
                ->setNumberHandover(1)
                ->setPositionHandover(1)
                ->setOffsetAccount($billingPosition)
                ->setAccountType(AccountingConstants::REALAX_POSITION_ACCOUNT_TYPE)
                ->setAccount(
                    $this
                        ->getLicenseVariable(
                            $billingMonth
                        )
                )
                ->setDebits(AccountingConstants::REALAX_POSITION_DEBITS)
                ->setBookingText(
                    sprintf(
                        static::BRANCH_CHARGE_DESCRIPTION,
                        $billingMonth
                            ->format('m'),
                        $billingMonth
                            ->format('y'),
                        $branch
                            ->getName()
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
                        ->getRealaxMoney($branchAmount)
                )
                ->setTaxAmount(
                    $this
                        ->getRealaxMoney($taxBranchAmount)
                )
            ->setQuantity(
                $quantity
            );

            $positions
                ->append($branchCharge);
        }

        return $positions;
    }

    /**
     * @return int[]
     */
    protected function getStateIdsByStateNames(): array
    {
        return $this
            ->salesFacade
            ->getStateIdsByStateNames(
                $this
                    ->config
                    ->getDeliveredState()
            );
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    protected function getFirstOfBillingMonth(): DateTime
    {
        return (new DateTime(static::FIRST_DAY_OF_BILLING_MONTH))
            ->setTime(
                0,
                0,
                0
            );
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    protected function getLastOfBillingMonth(): DateTime
    {
        return (new DateTime(static::LAST_DAY_OF_BILLING_MONTH))
            ->setTime(
                23,
                59,
                59
            );
    }

    /**
     * @param int $idBranch
     * @return int
     * @throws \Exception
     */
    protected function getSalesOrderCountByIdBranch(int $idBranch): int
    {
        $items = $this
            ->salesFacade
            ->getOrderItemsByBranchAndStateAndDateRange(
                $idBranch,
                $this->getStateIdsByStateNames(),
                $this->getFirstOfBillingMonth(),
                $this->getLastOfBillingMonth()
            );

        $quantity = 0;

        foreach ($items as $item) {
            $quantity += $item
                ->getQuantity();
        }

        return $quantity;
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
    protected function getLicenseVariable(
        DateTime $invoiceDate
    ): string
    {
        if ($this->isCoronaReducedTaxPeriod($invoiceDate) === true) {
            return $this
                ->config
                ->getLicenseInvoiceVariableReducedKey();
        }

        return $this
            ->config
            ->getLicenseInvoiceVariableKey();
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
