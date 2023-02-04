<?php

namespace Pyz\Zed\DeliveryArea;

use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Mail\MailConstants;
use Pyz\Shared\Oms\OmsConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class DeliveryAreaConfig extends AbstractBundleConfig
{
    protected const DEFAULT_TIME_FORMAT = 'H:i';
    protected const DEFAULT_DATE_TIME_FORMAT = 'D d.m.y H:i';
    protected const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';
    protected const DEFAULT_CONCRETE_TIME_SLOT_CREATION_LIMIT = '+14day';

    protected const EXPORT_CHUNK_SIZE = 100;
    protected const RECIPIENT_NAME = 'Händler';
    protected const EXPORT_CSV_ATTACHMENT_NAME = 'time-slots.csv';
    protected const QUEUE_KEY_ID_BRANCH = 'id-branch';
    protected const QUEUE_KEY_PAGE = 'page';
    protected const QUEUE_KEY_EMAIL = 'email';
    protected const QUEUE_KEY_FILENAME = 'filename';

    /**
     * @return string
     */
    public function getQueueKeyIdBranch(): string
    {
        return static::QUEUE_KEY_ID_BRANCH;
    }

    /**
     * @return string
     */
    public function getQueueKeyPage(): string
    {
        return static::QUEUE_KEY_PAGE;
    }

    /**
     * @return string
     */
    public function getQueueKeyEmail(): string
    {
        return static::QUEUE_KEY_EMAIL;
    }

    /**
     * @return string
     */
    public function getQueueKeyFilename(): string
    {
        return static::QUEUE_KEY_FILENAME;
    }

    /**
     * @return string
     */
    public function getExportRecipientName(): string
    {
        return static::RECIPIENT_NAME;
    }

    /**
     * @return string
     */
    public function getExportCsvAttachmentName(): string
    {
        return static::EXPORT_CSV_ATTACHMENT_NAME;
    }

    /**
     * @return string
     */
    public function getCsvFileTmpPath(): string
    {
        return $this
            ->get(DeliveryAreaConstants::DELIVERY_AREA_CSV_FILE_TMP_PATH);
    }

    /**
     * @return int
     */
    public function getExportChunkSize(): int
    {
        return static::EXPORT_CHUNK_SIZE;
    }

    /**
     * @return string
     */
    public function getTimeFormat(): string
    {
        return $this
            ->get(DeliveryAreaConstants::TIME_SLOT_TIME_FORMAT, self::DEFAULT_TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getDateTimeFormat(): string
    {
        return $this
            ->get(DeliveryAreaConstants::TIME_SLOT_DATE_TIME_FORMAT, self::DEFAULT_DATE_TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE, self::DEFAULT_PROJECT_TIME_ZONE);
    }

    /**
     * @return string
     */
    public function getAcceptedOmsState(): string
    {
        return $this
            ->get(OmsConstants::OMS_RETAIL_ACCEPTED_STATE);
    }

    /**
     * @return string
     */
    public function getConcreteTimeSlotCreationLimit(): string
    {
        return $this
            ->get(DeliveryAreaConstants::CONCRETE_TIME_SLOT_CREATION_LIMIT, static::DEFAULT_CONCRETE_TIME_SLOT_CREATION_LIMIT);
    }

    /**
     * @return array
     */
    public function getMaxCustomerAndProductValidationStateBlackList(): array
    {
        return $this
            ->get(DeliveryAreaConstants::MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST);
    }

    /**
     * @return string
     */
    public function getTimeSlotCsvImportPath(): string
    {
        return $this
            ->get(DeliveryAreaConstants::DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_UPLOAD_FOLDER);
    }

    /**
     * @return string
     */
    public function getDeliveryAreaFieldName(): string
    {
        return 'delivery-area';
    }

    /**
     * @return array
     */
    public function getDeveloperMailRecipient(): array
    {
        return $this
            ->get(MailConstants::MAIL_RECIPIENT_DEVELOPER);
    }
}
