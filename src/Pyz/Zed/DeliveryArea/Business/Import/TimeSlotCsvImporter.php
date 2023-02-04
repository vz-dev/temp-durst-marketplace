<?php
/**
 * Durst - project - TimeSlotCsvImporter.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-14
 * Time: 12:37
 */

namespace Pyz\Zed\DeliveryArea\Business\Import;

use DateTime;
use Exception;
use Throwable;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ExceptionTransfer;
use Generated\Shared\Transfer\MailAttachmentTransfer;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\TimeSlotTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotCsvImporterException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotDeletedException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotDoesNotBelongToBranchException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotInvalidTimeFormatException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotMalformedTimeStringException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartOrEndNotSetException;
use Pyz\Zed\DeliveryArea\Business\Exception\TimeSlotStartTimeBiggerEqualThanEndTimeException;
use Pyz\Zed\DeliveryArea\Business\Export\CsvTimeSlotExporter;
use Pyz\Zed\DeliveryArea\Business\Model\TimeSlot;
use Pyz\Zed\DeliveryArea\Business\Repository\DeliveryAreaRepository;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportFailedDevelopersMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportFailedMerchantMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportSuccessMailTypePlugin;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Shared\ErrorHandler\ErrorLoggerInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

/**
 * Class TimeSlotCsvImporter
 * @package Pyz\Zed\DeliveryArea\Business\Import
 */
class TimeSlotCsvImporter implements TimeSlotCsvImporterInterface
{
    public const IMPORT_DATA_KEY_FILE = 'csv';
    public const IMPORT_DATA_KEY_ID_BRANCH = 'id_branch';
    public const IMPORT_DATA_KEY_EMAIL = 'email';

    public const IMPORT_CSV_DELIMITER = ';';

    public const HEADER_ID = 'Zeitfenster ID';
    public const HEADER_ZIP_CODE = 'PLZ';
    public const HEADER_START = 'Startzeitpunkt';
    public const HEADER_END = 'Endzeitpunkt';
    public const HEADER_MON = 'Montag';
    public const HEADER_TUE = 'Dienstag';
    public const HEADER_WED = 'Mittwoch';
    public const HEADER_THU = 'Donnerstag';
    public const HEADER_FRI = 'Freitag';
    public const HEADER_SAT = 'Samstag';
    public const HEADER_ACTIVE = 'aktiv/inaktiv';
    public const HEADER_PREP_TIME = 'Vorlaufzeit';
    public const HEADER_MIN_FIRST = 'Mindestbestellwert';
    public const HEADER_OPTIONAL_MAX_CUSTOMERS = 'Maximalanzahl Kunden';

    public const HEADER_INTEGRA_TOUR_NO = 'TourNr';
    public const HEADER_INTEGRA_DELIVERY_WINDOW_NO = 'ZeitfensterNr';

    public const TIME_SLOT_CSV_INT_FIELDS = [
        self::HEADER_PREP_TIME,
        self::HEADER_MIN_FIRST,
        self::HEADER_OPTIONAL_MAX_CUSTOMERS,
        self::HEADER_ACTIVE,
    ];

    public const TIME_SLOT_CSV_BOOL_FIELDS = [
        self::HEADER_MON,
        self::HEADER_TUE,
        self::HEADER_WED,
        self::HEADER_THU,
        self::HEADER_FRI,
        self::HEADER_SAT,
    ];

    public const TIME_SLOT_CSV_TIME_FORMAT = 'H:i';
    public const TIME_SLOT_MAX_PRODUCTS_DEFAULT_VALUE = 999;

    public const DEFAULT_VALUES_OPTIONAL_FIELDS = [
        self::HEADER_OPTIONAL_MAX_CUSTOMERS => 999,
    ];

    public const IMPORT_DELETE_VALUE = 3;

    public const IMPORT_TOTALS_ARRAY_KEY_TOTAL = 'total';
    public const IMPORT_TOTALS_ARRAY_KEY_NEW = 'new';
    public const IMPORT_TOTALS_ARRAY_KEY_UPDATED = 'updated';
    public const IMPORT_TOTALS_ARRAY_KEY_DELETED = 'deleted';

    /**
     * @var DeliveryAreaConfig
     */
    protected $config;

    /**
     * @var DeliveryAreaQueryContainer
     */
    protected $queryContainer;

    /**
     * @var TimeSlot
     */
    protected $timeSlot;

    /**
     * @var DeliveryAreaRepository
     */
    protected  $deliveryAreaRepository;

    /**
     * @var MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var ErrorLoggerInterface
     */
    protected $errorLogger;

    /**
     * @var array
     */
    protected $importData;

    /**
     * @var BranchTransfer
     */
    protected $branchTransfer;

    /**
     * @var int
     */
    protected $importsTotal = 0;

    /**
     * @var int
     */
    protected $importNew = 0;

    /**
     * @var int
     */
    protected $importUpdated = 0;

    /**
     * @var int
     */
    protected $importDeleted = 0;

    /**
     * TimeSlotCsvImporter constructor.
     *
     * @param DeliveryAreaConfig $config
     * @param DeliveryAreaQueryContainer $queryContainer
     * @param TimeSlot $timeSlot
     * @param DeliveryAreaRepository $deliveryAreaRepository
     * @param MailFacadeInterface $mailFacade
     * @param MerchantFacadeInterface $merchantFacade
     * @param ErrorLoggerInterface $errorLogger
     */
    public function __construct(
        DeliveryAreaConfig         $config,
        DeliveryAreaQueryContainer $queryContainer,
        TimeSlot                   $timeSlot,
        DeliveryAreaRepository     $deliveryAreaRepository,
        MailFacadeInterface        $mailFacade,
        MerchantFacadeInterface    $merchantFacade,
        ErrorLoggerInterface       $errorLogger
    ) {
        $this->config = $config;
        $this->queryContainer = $queryContainer;
        $this->timeSlot = $timeSlot;
        $this->deliveryAreaRepository = $deliveryAreaRepository;
        $this->mailFacade = $mailFacade;
        $this->merchantFacade = $merchantFacade;
        $this->errorLogger = $errorLogger;
    }

    /**
     * @param string $jsonData
     * @throws Exception
     */
    public function importTimeSlotsFromCsv(string $jsonData)
    {
        $this->importData = $this->decodeJson($jsonData);
        $this->branchTransfer = $this->getBranchById($this->importData);

        $file = $this->getFileNameWithPath($this->importData);

        $data = $this->convertCsvToArray($file);

        $this
            ->queryContainer
            ->getConnection()
            ->beginTransaction();

        try {
            $lastRow = 1;
            $lastItem = null;

            foreach ($data as $item) {
                $lastRow++;
                $lastItem = $item;

                $this->createTimeSlot($item);
            }

            $this->sendSuccessMail();
        } catch (Exception $exception) {
            $this->queryContainer->getConnection()->rollBack();

            $importException = (new TimeSlotCsvImporterException(null, null, $exception))
                ->setFailedRow($lastRow)
                ->setFailedItem($lastItem);

            $this->handleError($importException);

            throw $exception;
        }

        $this
            ->queryContainer
            ->getConnection()
            ->commit();

        return;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function convertCsvToArray(string $fileName) : array
    {
        $fileArray = file($fileName);

        if (substr($fileArray[0], 0, 3) === utf8_decode("\u{ef}\u{bb}\u{bf}")) {
            $fileArray[0] = substr($fileArray[0], 3);
        }

        $rows = array_map(function ($v) {
            return str_getcsv($v, static::IMPORT_CSV_DELIMITER);
        }, $fileArray);

        $header = array_shift($rows);
        $csv = [];
        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }

        $this->importsTotal = count($rows);

        return $csv;
    }

    /**
     * @param array $timeSlotData
     *
     * @return void
     *
     * @throws DeliveryAreaNotFoundException
     * @throws TimeSlotDeletedException
     * @throws TimeSlotDoesNotBelongToBranchException
     * @throws TimeSlotInvalidTimeFormatException
     * @throws TimeSlotMalformedTimeStringException
     * @throws TimeSlotNotFoundException
     * @throws TimeSlotStartOrEndNotSetException
     * @throws TimeSlotStartTimeBiggerEqualThanEndTimeException
     */
    protected function createTimeSlot(array $timeSlotData)
    {
        $timeSlotData = $this->prepTimeSlotData($timeSlotData);

        if ($timeSlotData[static::HEADER_ID] !== null) {
            $existingTimeSlot = $this->findExistingTimeSlot($timeSlotData[static::HEADER_ID]);

            if ($this->checkIfTimeSlotBelongsToBranch($existingTimeSlot) === false ) {
                throw new TimeSlotDoesNotBelongToBranchException(
                    sprintf(
                        TimeSlotDoesNotBelongToBranchException::MESSAGE,
                        $timeSlotData[static::HEADER_ID],
                        $this->branchTransfer->getIdBranch()
                    )
                );
            }
        }

        if ($timeSlotData[static::HEADER_ACTIVE] === static::IMPORT_DELETE_VALUE &&
            is_numeric($timeSlotData[static::HEADER_ID]) === true
        ) {
            $this
                ->timeSlot
                ->removeTimeSlot($timeSlotData[static::HEADER_ID]);

            $this->importDeleted += 1;

            return;
        }

        $timeSlotTransfer = (new TimeSlotTransfer())
            ->setIdTimeSlot($timeSlotData[static::HEADER_ID])
            ->setFkBranch($this->importData[static::IMPORT_DATA_KEY_ID_BRANCH])
            ->setFkDeliveryArea($timeSlotData[static::HEADER_ZIP_CODE])
            ->setStartTime($this->formatTimeField(static::HEADER_START, $timeSlotData[static::HEADER_START]))
            ->setEndTime($this->formatTimeField(static::HEADER_END, $timeSlotData[static::HEADER_END]))
            ->setMonday($timeSlotData[static::HEADER_MON])
            ->setTuesday($timeSlotData[static::HEADER_TUE])
            ->setWednesday($timeSlotData[static::HEADER_WED])
            ->setThursday($timeSlotData[static::HEADER_THU])
            ->setFriday($timeSlotData[static::HEADER_FRI])
            ->setSaturday($timeSlotData[static::HEADER_SAT])
            ->setIsActive((bool)$timeSlotData[static::HEADER_ACTIVE])
            ->setMinValueFirst($timeSlotData[static::HEADER_MIN_FIRST])
            ->setMaxProducts(self::TIME_SLOT_MAX_PRODUCTS_DEFAULT_VALUE)
            ->setMaxCustomers($this->getOptionalFieldsValueOrDefault(static::HEADER_OPTIONAL_MAX_CUSTOMERS, $timeSlotData));

        if($this->branchTransfer->getOrderOnTimeslot() === true){
            $timeSlotTransfer
                ->setIntegraTourNo($timeSlotData[static::HEADER_INTEGRA_TOUR_NO])
                ->setIntegraDeliveryWindowNo($timeSlotData[static::HEADER_INTEGRA_DELIVERY_WINDOW_NO]);
        }

        if ($timeSlotData[static::HEADER_ID] === null) {
            $timeSlotTransfer->setPrepTime($timeSlotData[static::HEADER_PREP_TIME]);
        }

        $this
            ->timeSlot
            ->save($timeSlotTransfer);

        if ($timeSlotData[static::HEADER_ID] !== null) {
            $this->importUpdated += 1;
            return;
        }

        $this->importNew += 1;
    }

    /**
     * @param array $timeSlotData
     *
     * @return array
     *
     * @throws DeliveryAreaNotFoundException
     */
    protected function prepTimeSlotData(array $timeSlotData) : array
    {
        foreach ($timeSlotData as $key => $value) {
            if ($key === static::HEADER_ID) {
                if ($value == '') {
                    $timeSlotData[static::HEADER_ID] = null;
                    continue;
                }

                $timeSlotData[static::HEADER_ID] = (int)($timeSlotData[static::HEADER_ID]);
            }

            if ($key === static::HEADER_ZIP_CODE) {
                $timeSlotData[static::HEADER_ZIP_CODE] = $this->getDeliveryAreaFkFromZip($timeSlotData[static::HEADER_ZIP_CODE]);
                continue;
            }

            if (in_array($key, self::DEFAULT_VALUES_OPTIONAL_FIELDS) === true && $timeSlotData[$key] === '') {
                $timeSlotData[$key] = null;
                continue;
            }

            if (in_array($key, self::TIME_SLOT_CSV_INT_FIELDS) === true) {
                $timeSlotData[$key] = (int)$value;
                continue;
            }

            if (in_array($key, self::TIME_SLOT_CSV_BOOL_FIELDS) === true) {
                $timeSlotData[$key] = (bool)$value;
                continue;
            }
        }

        return $timeSlotData;
    }

    /**
     * @param string $field
     * @param array $timeSlotData
     *
     * @return int
     */
    protected function getOptionalFieldsValueOrDefault(string $field, array $timeSlotData) : int
    {
        if (array_key_exists($field, $timeSlotData) === true && $timeSlotData[$field] !== null) {
            return (int)($timeSlotData[$field]);
        }

        return static::DEFAULT_VALUES_OPTIONAL_FIELDS[$field];
    }

    /**
     * @param string $zipCode
     * @return int
     * @throws DeliveryAreaNotFoundException
     */
    protected function getDeliveryAreaFkFromZip(string $zipCode) : int
    {
        return $this
            ->deliveryAreaRepository
            ->getDeliveryAreaIdByZip($zipCode);
    }

    /**
     * @param array $importData
     *
     * @return string
     */
    protected function getFileNameWithPath(array $importData) : string
    {
        return sprintf(
            '%s%d/%s',
            $this->config->getTimeSlotCsvImportPath(),
            $importData[static::IMPORT_DATA_KEY_ID_BRANCH],
            $importData[static::IMPORT_DATA_KEY_FILE]
        );
    }

    /**
     * @param string $json
     *
     * @return array
     */
    protected function decodeJson(string $json): array
    {
        return json_decode($json, true);
    }

    /**
     * @return void
     */
    protected function sendSuccessMail(): void
    {
        $mailTransfer = $this->createMailTransfer();
        $mailTransfer
            ->setType(TimeSlotImportSuccessMailTypePlugin::NAME)
            ->setTimeSlotCsvImportResults($this->getImportTotals())
            ->setBranch($this->branchTransfer);

        $dispatcherEmail = $this->branchTransfer->getDispatcherEmail();

        $userRecipient = (new MailRecipientTransfer())
            ->setEmail($this->importData[static::IMPORT_DATA_KEY_EMAIL]);

        $mailTransfer
            ->setEmail($dispatcherEmail)
            ->addRecipient($userRecipient);

        $this->sendMail($mailTransfer);
    }

    /**
     * @param TimeSlotCsvImporterException $exception
     */
    protected function handleError(TimeSlotCsvImporterException $exception)
    {
        $this->sendFailedMailToMerchant($exception);
        $this->sendFailedMailToDevelopers($exception);

        $this->errorLogger->log($exception);
    }

    /**
     * @param TimeSlotCsvImporterException $exception
     */
    protected function sendFailedMailToMerchant(TimeSlotCsvImporterException $exception): void
    {
        $exceptionTransfer = $this->createExceptionTransfer($exception);

        $mailTransfer = $this->createMailTransfer();
        $mailTransfer
            ->setType(TimeSlotImportFailedMerchantMailTypePlugin::NAME)
            ->setBranch($this->branchTransfer)
            ->addAttachment($this->createAttachmentTransfer())
            ->setException($exceptionTransfer);

        $dispatcherEmail = $this->branchTransfer->getDispatcherEmail();

        $userRecipient = (new MailRecipientTransfer())
            ->setEmail($this->importData[static::IMPORT_DATA_KEY_EMAIL]);

        $mailTransfer
            ->setEmail($dispatcherEmail)
            ->addRecipient($userRecipient);

        $this->sendMail($mailTransfer);
    }

    /**
     * @param Exception $exception
     * @return void
     */
    protected function sendFailedMailToDevelopers(Exception $exception)
    {
        $exceptionTransfer = $this->createExceptionTransfer($exception);

        $mailTransfer = $this->createMailTransfer();
        $mailTransfer
            ->setType(TimeSlotImportFailedDevelopersMailTypePlugin::NAME)
            ->setEmail($this->config->getDeveloperMailRecipient()['email'])
            ->setBranch($this->branchTransfer)
            ->setException($exceptionTransfer);

        $this->sendMail($mailTransfer);
    }

    /**
     * @return MailAttachmentTransfer
     */
    protected function createAttachmentTransfer(): MailAttachmentTransfer
    {
        return (new MailAttachmentTransfer())
            ->setAttachmentUrl($this->getFilenameWithPath($this->importData))
            ->setFileName($this->importData[static::IMPORT_DATA_KEY_FILE]);
    }

    /**
     * @param MailTransfer $mailTransfer
     *
     * @return void
     */
    protected function sendMail(MailTransfer $mailTransfer)
    {
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
     * @param array $importData
     *
     * @return BranchTransfer
     */
    protected function getBranchById(array $importData) : BranchTransfer
    {
        return $this
            ->merchantFacade
            ->getBranchById($importData[static::IMPORT_DATA_KEY_ID_BRANCH]);
    }

    /**
     * @return array
     */
    protected function getImportTotals() : array
    {
        return [
            self::IMPORT_TOTALS_ARRAY_KEY_TOTAL => $this->importsTotal,
            self::IMPORT_TOTALS_ARRAY_KEY_NEW => $this->importNew,
            self::IMPORT_TOTALS_ARRAY_KEY_UPDATED => $this->importUpdated,
            self::IMPORT_TOTALS_ARRAY_KEY_DELETED => $this->importDeleted,
        ];
    }

    /**
     * @param TimeSlotTransfer $timeSlot
     *
     * @return bool
     */
    protected function checkIfTimeSlotBelongsToBranch(TimeSlotTransfer $timeSlot) : bool
    {
        return ($timeSlot->getFkBranch() == $this->branchTransfer->getIdBranch());
    }

    /**
     * @param int $idTimeSlot
     *
     * @return TimeSlotTransfer
     *
     * @throws TimeSlotDeletedException
     * @throws TimeSlotNotFoundException
     */
    protected function findExistingTimeSlot(int $idTimeSlot): TimeSlotTransfer
    {
        $existingTimeSlot = $this
            ->timeSlot
            ->getTimeSlotById($idTimeSlot);

        if ($existingTimeSlot->getStatus() === SpyTimeSlotTableMap::COL_STATUS_DELETED) {
            throw new TimeSlotDeletedException(sprintf(
                TimeSlotDeletedException::MESSAGE, $idTimeSlot
            ));
        }

        return $existingTimeSlot;
    }

    /**
     * @param Throwable $exception
     *
     * @return ExceptionTransfer
     */
    protected function createExceptionTransfer(Throwable $exception): ExceptionTransfer
    {
        $exceptionTransfer = (new ExceptionTransfer())
            ->setClassName(get_class($exception))
            ->setMessage($exception->getMessage())
            ->setFile($exception->getFile())
            ->setLine($exception->getLine())
            ->setTraceString($exception->getTraceAsString());

        if ($exception->getPrevious() !== null) {
            $exceptionTransfer->setPrevious(
                $this->createExceptionTransfer($exception->getPrevious())
            );
        }

        if ($exception instanceof TimeSlotCsvImporterException) {
            $exceptionTransfer
                ->setFailedRow($exception->getFailedRow())
                ->setFailedItem($exception->getFailedItem());
        }

        return $exceptionTransfer;
    }

    /**
     * @param string $fieldName
     * @param string $value
     *
     * @return string
     *
     * @throws TimeSlotInvalidTimeFormatException
     */
    protected function formatTimeField(string $fieldName, string $value)
    {
        if ($value === '' || $value === CsvTimeSlotExporter::NA_STRING) {
            return null;
        }

        $valueDateTime = DateTime::createFromFormat(self::TIME_SLOT_CSV_TIME_FORMAT, $value);

        if ($valueDateTime === false) {
            throw new TimeSlotInvalidTimeFormatException(sprintf(
                TimeSlotInvalidTimeFormatException::MESSAGE,
                $fieldName
            ));
        }

        $result = $valueDateTime->format(DateTime::ATOM);

        return $result;
    }
}
