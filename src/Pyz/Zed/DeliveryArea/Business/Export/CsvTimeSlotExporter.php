<?php
/**
 * Durst - project - CsvTimeSlotExporter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.09.20
 * Time: 13:54
 */

namespace Pyz\Zed\DeliveryArea\Business\Export;

use DateTime;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailAttachmentTransfer;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Util\PropelModelPager;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotExportMailTypePlugin;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Client\Queue\QueueClientInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

class CsvTimeSlotExporter implements CsvTimeSlotExporterInterface
{
    public const NA_STRING = 'n/a';

    protected const HEADER_ID = 'Zeitfenster ID';
    protected const HEADER_ZIP_CODE = 'PLZ';
    protected const HEADER_START = 'Startzeitpunkt';
    protected const HEADER_END = 'Endzeitpunkt';
    protected const HEADER_MON = 'Montag';
    protected const HEADER_TUE = 'Dienstag';
    protected const HEADER_WED = 'Mittwoch';
    protected const HEADER_THU = 'Donnerstag';
    protected const HEADER_FRI = 'Freitag';
    protected const HEADER_SAT = 'Samstag';
    protected const HEADER_ACTIVE = 'aktiv/inaktiv';
    protected const HEADER_PREP_TIME = 'Vorlaufzeit';
    protected const HEADER_MIN_FIRST = 'Mindestbestellwert';

    protected const HEADER_OPTIONAL_MAX_CUSTOMERS = 'Maximalanzahl Kunden';

    public const HEADER_INTEGRA_TOUR_NO = 'TourNr';
    public const HEADER_INTEGRA_DELIVERY_WINDOW_NO = 'ZeitfensterNr';

    protected const DATE_FORMAT = 'H:i';
    protected const DELIMITER = ';';

    protected static $iterations = 0;

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var DeliveryAreaConfig
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * @var QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var BranchTransfer
     */
    protected $branchTransfer;


    /**
     * CsvTimeSlotExporter constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     * @param DeliveryAreaConfig $config
     * @param Filesystem $fileSystem
     * @param MailFacadeInterface $mailFacade
     * @param QueueClientInterface $queueFacade
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        DeliveryAreaQueryContainerInterface $queryContainer,
        DeliveryAreaConfig $config,
        Filesystem $fileSystem,
        MailFacadeInterface $mailFacade,
        QueueClientInterface $queueFacade,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->fileSystem = $fileSystem;
        $this->mailFacade = $mailFacade;
        $this->queueClient = $queueFacade;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param int $idBranch
     * @param array $emails
     * @param int $page
     * @param string|null $filename
     *
     * @return void
     */
    public function writeChunk(int $idBranch, array $emails, int $page, ?string $filename = null): void
    {
        $this->branchTransfer = $this->getBranchById($idBranch);
        $entities = $this->getEntityPage($idBranch, $page);
        $data = $this->prepareDataArray($entities);
        $transformedData = $this->transformDataArray($data, $page);
        if ($filename === null) {
            $filename = $this->createUniqueFilename();
        }
        $this->writePageToFile($transformedData, $filename);

        if ($page >= $entities->getLastPage()) {
            $this->sendEmail($emails, $filename);
            $this->deleteTmpFile($filename);
            return;
        }

        $this->queueNextPage($idBranch, $page, $emails, $filename);
    }

    /**
     * @param string $filename
     *
     * @return void
     */
    protected function deleteTmpFile(string $filename): void
    {
        $this
            ->fileSystem
            ->remove($this->getFilenameWithPath($filename));
    }

    /**
     * @param array $emails
     * @param string $attachmentFilename
     *
     * @return void
     */
    protected function sendEmail(array $emails, string $attachmentFilename): void
    {
        $mailTransfer = $this->createMailTransfer();

        $mailTransfer->setEmail($emails[0]);
        foreach ($emails as $email)
        {
            $mailTransfer->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail($email)
                    ->setName($email)
            );
        }

        $mailTransfer->addAttachment($this->createAttachmentTransfer($attachmentFilename));

        $this->mailFacade->handleMail($mailTransfer);
    }

    /**
     * @param string $filename
     *
     * @return MailAttachmentTransfer
     */
    protected function createAttachmentTransfer(string $filename): MailAttachmentTransfer
    {
        return (new MailAttachmentTransfer())
            ->setAttachmentUrl($this->getFilenameWithPath($filename))
            ->setDisplayName($this->config->getExportCsvAttachmentName())
            ->setFileName($this->config->getExportCsvAttachmentName());
    }

    /**
     * @return MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        return (new MailTransfer())
            ->setType(TimeSlotExportMailTypePlugin::NAME);
    }

    /**
     * @param int $idBranch
     * @param int $page
     * @param string $email
     * @param string $filename
     *
     * @return void
     */
    protected function queueNextPage(
        int $idBranch,
        int $page,
        array $emails,
        string $filename
    ): void {
        $this
            ->queueClient
            ->sendMessage(
                DeliveryAreaConstants::DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME,
                $this->prepareQueueMessage($idBranch, $page + 1, $emails, $filename)
            );
    }

    /**
     * @param int $idBranch
     * @param int $page
     * @param string $email
     * @param string $filename
     *
     * @return QueueSendMessageTransfer
     */
    protected function prepareQueueMessage(
        int $idBranch,
        int $page,
        array $emails,
        string $filename
    ): QueueSendMessageTransfer {
        return (new QueueSendMessageTransfer())
            ->setBody(
                json_encode([
                    $this->config->getQueueKeyIdBranch() => $idBranch,
                    $this->config->getQueueKeyPage() => $page,
                    $this->config->getQueueKeyEmail() => $emails,
                    $this->config->getQueueKeyFilename() => $filename,
                ])
            );
    }

    /**
     * @param array $data
     * @param int $page
     *
     * @return array
     */
    protected function transformDataArray(array $data, int $page): array
    {
        $transformedArray = [];
        if ($page === 1) {
            $transformedArray[] = $this->getCsvHeader();
        }

        foreach ($data as $row) {
            $transformedArray[] = $this->transformRow($row);
        }

        return $transformedArray;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    protected function transformRow(array $row): array
    {
        $result = [];
        foreach ($this->getCsvHeader() as $column) {
            $result[] = $row[$column];
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function createUniqueFilename(): string
    {
        $filename = sprintf(
            '%s.csv',
            uniqid()
        );
        if ($this->fileSystem->exists($this->getFilenameWithPath($filename)) === true && CsvTimeSlotExporter::$iterations < 10) {
            CsvTimeSlotExporter::$iterations++;
            return $this->createUniqueFilename();
        }

        return $filename;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getFilenameWithPath(string $filename): string
    {
        return sprintf(
            '%s/%s',
            $this->getFilepath(),
            $filename
        );
    }

    /**
     * @return string
     */
    protected function getFilepath(): string
    {
        if ($this->fileSystem->exists($this->config->getCsvFileTmpPath()) !== true) {
            $this->fileSystem->mkdir($this->config->getCsvFileTmpPath());
        }

        return $this->config->getCsvFileTmpPath();
    }

    /**
     * @param array $data
     * @param string $filename
     *
     * @return void
     */
    protected function writePageToFile(array $data, string $filename)
    {
        $handle = fopen($this->getFilenameWithPath($filename), 'a');
        try {
            foreach ($data as $row) {
                fputcsv(
                    $handle,
                    $row,
                    static::DELIMITER
                );
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param iterable|SpyTimeSlot[] $timeSlotData
     *
     * @return array
     */
    protected function prepareDataArray(iterable $timeSlotData): array
    {
        $dataSets = [];
        foreach ($timeSlotData as $timeSlotDatum) {
            $dataSet = [
                static::HEADER_ID => $timeSlotDatum->getIdTimeSlot(),
                static::HEADER_ZIP_CODE => $timeSlotDatum->getSpyDeliveryArea()->getZipCode(),
                static::HEADER_START => $this->getFormattedDate($timeSlotDatum->getStartTime()),
                static::HEADER_END => $this->getFormattedDate($timeSlotDatum->getEndTime()),
                static::HEADER_MON => $timeSlotDatum->getMonday(),
                static::HEADER_TUE => $timeSlotDatum->getTuesday(),
                static::HEADER_WED => $timeSlotDatum->getWednesday(),
                static::HEADER_THU => $timeSlotDatum->getThursday(),
                static::HEADER_FRI => $timeSlotDatum->getFriday(),
                static::HEADER_SAT => $timeSlotDatum->getSaturday(),
                static::HEADER_ACTIVE => $timeSlotDatum->getIsActive(),
                static::HEADER_PREP_TIME => $timeSlotDatum->getPrepTime(),
                static::HEADER_MIN_FIRST => $timeSlotDatum->getMinValueFirst(),
                static::HEADER_OPTIONAL_MAX_CUSTOMERS => $timeSlotDatum->getMaxCustomers(),
            ];

            if($this->branchTransfer->getOrderOnTimeslot() === true)
            {
                $dataSet[static::HEADER_INTEGRA_TOUR_NO] = $timeSlotDatum->getIntegraTourNo();
                $dataSet[static::HEADER_INTEGRA_DELIVERY_WINDOW_NO] = $timeSlotDatum->getIntegraDeliveryWindowNo();
            }

            $dataSets[] = $dataSet;
        }

        return $dataSets;
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return string
     */
    protected function getFormattedDate(?DateTime $dateTime = null): string
    {
        if ($dateTime === null) {
            return static::NA_STRING;
        }

        return $dateTime->format(static::DATE_FORMAT);
    }

    /**
     * @return string[]
     */
    protected function getCsvHeader(): array
    {
        $headers = [
            static::HEADER_ID,
            static::HEADER_ZIP_CODE,
            static::HEADER_START,
            static::HEADER_END,
            static::HEADER_MON,
            static::HEADER_TUE,
            static::HEADER_WED,
            static::HEADER_THU,
            static::HEADER_FRI,
            static::HEADER_SAT,
            static::HEADER_ACTIVE,
            static::HEADER_PREP_TIME,
            static::HEADER_MIN_FIRST,
            static::HEADER_OPTIONAL_MAX_CUSTOMERS,
        ];

        if($this->branchTransfer->getOrderOnTimeslot() === true)
        {
            $headers[] = static::HEADER_INTEGRA_TOUR_NO;
            $headers[] = static::HEADER_INTEGRA_DELIVERY_WINDOW_NO;
        }

        return $headers;
    }

    /**
     * @param int $idBranch
     * @param int $page
     *
     * @return iterable|SpyTimeSlot[]|PropelModelPager
     */
    protected function getEntityPage(int $idBranch, int $page): iterable
    {
        return $this
            ->queryContainer
            ->queryTimeSlot()
            ->useSpyDeliveryAreaQuery()
            ->endUse()
            ->filterByFkBranch($idBranch)
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_DELETED, Criteria::NOT_EQUAL)
            ->orderByIdTimeSlot(Criteria::ASC)
            ->paginate($page, $this->config->getExportChunkSize());
    }

    /**
     * @param int $idBranch
     * @return BranchTransfer
     */
    protected function getBranchById(int $idBranch) : BranchTransfer
    {
        return $this
            ->merchantFacade
            ->getBranchById($idBranch);
    }
}
