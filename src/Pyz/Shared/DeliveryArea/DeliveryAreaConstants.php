<?php
/**
 * Durst - project - DeliveryAreaConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 17:03
 */

namespace Pyz\Shared\DeliveryArea;


interface DeliveryAreaConstants
{
    public const TIME_SLOT_TIME_FORMAT = 'TIME_SLOT_TIME_FORMAT';
    public const TIME_SLOT_DATE_TIME_FORMAT = 'TIME_SLOT_DATE_TIME_FORMAT';

    public const DELIVERY_COST_EXPENSE_TYPE = 'DELIVERY_COST_EXPENSE_TYPE';
    public const DELIVERY_COST_EXPENSE_NAME = 'Liefergebühr';

    public const ELASTICSEARCH_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    public const ELASTICSEARCH_TIMESTAMP_FORMAT = 'U';

    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a concrete time slot
     */
    public const RESOURCE_TYPE_CONCRETE_TIME_SLOT = 'RESOURCE_TYPE_CONCRETE_TIME_SLOT';


    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a delivery area
     */
    public const RESOURCE_TYPE_DELIVERY_AREA = 'RESOURCE_TYPE_DELIVERY_AREA';

    /**
     * Name of deliver area type inside Elasticsearch
     */
    public const DELIVERY_AREA_SEARCH_TYPE = 'delivery_area';

    /**
     * Name of timeslot type inside Elasticsearch
     */
    public const TIMESLOT_SEARCH_TYPE = 'timeslot';

    /**
     * used for Timeslots
     */
    public const TIMESLOT_KEY_START_TIME = 'start_time';
    public const TIMESLOT_KEY_END_TIME = 'end_time';
    public const TIMESLOT_DATE_FORMAT_SEARCH = 'Y-m-d H:i:s';

    /**
     * Sets the limit how far into the future concrete time slots should be created beforehand
     */
    public const CONCRETE_TIME_SLOT_CREATION_LIMIT = 'CONCRETE_TIME_SLOT_CREATION_LIMIT';

    public const MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST = 'MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST';

    /**
     * location where files will be stored temporarily until they get sent via email.
     */
    public const DELIVERY_AREA_CSV_FILE_TMP_PATH = 'DELIVERY_AREA_CSV_FILE_TMP_PATH';

    /**
     * identifier for the queue that exports time slots as csv files.
     */
    public const DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME = 'time-slot-csv-export';
    public const DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME_ERROR = 'time-slot-csv-export.error';

    /**
     * Queue names time slot import
     */
    public const DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_QUEUE_NAME = 'time-slot-csv-import';
    public const DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_QUEUE_NAME_ERROR = 'time-slot-csv-import.error';

    public const DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_UPLOAD_FOLDER = 'DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_UPLOAD_FOLDER';
}
