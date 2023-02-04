<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.11.18
 * Time: 14:44
 */

namespace Pyz\Shared\Tour;


interface TourConstants
{
    public const TOUR_DATE_FORMAT = 'TOUR_DATE_FORMAT';
    public const TOUR_ACTIVE_PROCESSES  = 'TOUR_ACTIVE_PROCESSES';
    public const TOUR_STATE_BLACK_LIST  = 'TOUR_STATE_BLACK_LIST';

    public const TOUR_HIDDEN_STATES = 'TOUR_HIDDEN_STATES';

    public const TOUR_EXPORTER_PATH = 'TOUR_EXPORTER_PATH';

    public const EDI_EXPORT_PROCESS_TIMEOUT = 6000;

    public const EDI_CLIENT_CURL_OPTIONS = 'EDI_CLIENT_CURL_OPTIONS';

    public const DURST_ILN = 'DURST_ILN';

    public const EDIFACT_TESTRUN = 'EDIFACT_TESTRUN';

    public const PHP_PATH_FOR_CONSOLE = 'PHP_PATH_FOR_CONSOLE';

    public const CONCRETE_TOUR_FILTERING_EARLIEST_ALLOWED_DATE = 'CONCRETE_TOUR_FILTERING_EARLIEST_ALLOWED_DATE';

    public const TOUR_STATE_MACHINE_PROCESS = 'TOUR_STATE_MACHINE_PROCESS';

    public const TOUR_INITIAL_STATE = 'TOUR_INITIAL_STATE';

    public const DRIVER_APP_TOUR_FUTURE_CUTOFF = 'DRIVER_APP_TOUR_FUTURE_CUTOFF';
    public const DRIVER_APP_TOUR_PAST_CUTOFF = 'DRIVER_APP_TOUR_PAST_CUTOFF';

    public const TOUR_STATE_NEW = 'new';
    public const TOUR_STATE_ORDERABLE = 'orderable';
    public const TOUR_STATE_DELETED = 'deleted';
    public const TOUR_STATE_NO_VALID_ORDERS = 'no valid orders';
    public const TOUR_STATE_GOODS_EXPORTABLE = 'goods exportable';
    public const TOUR_STATE_GOODS_EXPORT_FAILED = 'goods export failed';
    public const TOUR_STATE_MERCHANT_NOTIFIED_GOODS = 'merchant notified goods';
    public const TOUR_STATE_GOODS_EXPORTED = 'goods exported';
    public const TOUR_STATE_IN_PLANNING = 'in planning';
    public const TOUR_STATE_IN_DELIVERY = 'in delivery';
    public const TOUR_STATE_LOADING = 'loading';
    public const TOUR_STATE_JOURNEY_THERE = 'journey there';
    public const TOUR_STATE_EXPORTABLE_RETURNS = 'exportable returns';
    public const TOUR_STATE_RETURN_EXPORTABLE_AUTO = 'return exportable auto';
    public const TOUR_STATE_RETURN_EXPORTABLE_MANUAL = 'return exportable manual';
    public const TOUR_STATE_MERCHANT_NOTIFIED_RETURN = 'merchant notified return';
    public const TOUR_STATE_RETURN_EXPORTED = 'return exported';
    public const TOUR_STATE_RETURN_EXPORT_FAILED = 'return export failed';
    public const TOUR_STATE_RETURN_JOURNEY = 'return journey';
    public const TOUR_STATE_UNLOADING = 'unloading';
    public const TOUR_STATE_FINISHED = 'finished';

    public const TOUR_STATE_EVENT_CREATE = 'create';
    public const TOUR_STATE_EVENT_DELETE = 'delete';
    public const TOUR_STATE_EVENT_ORDER = 'order';
    public const TOUR_STATE_EVENT_EXPORT_GOODS = 'export goods';
    public const TOUR_STATE_EVENT_RETRY_EXPORT_GOODS = 'retry export goods';
    public const TOUR_STATE_EVENT_NOTIFY_MERCHANT_GOODS = 'notify merchant goods';
    public const TOUR_STATE_EVENT_PLAN = 'plan';
    public const TOUR_STATE_EVENT_DELIVER = 'deliver';
    public const TOUR_STATE_EVENT_LOAD = 'load';
    public const TOUR_STATE_EVENT_START_JOURNEY = 'start journey';
    public const TOUR_STATE_EVENT_START_DELIVERY = 'start delivery';
    public const TOUR_STATE_EVENT_EXPORT_RETURN = 'export return';
    public const TOUR_STATE_EVENT_FINISH_DELIVERY = 'finish delivery';
    public const TOUR_STATE_EVENT_EXPORT_RETURN_AUTO = 'export return auto';
    public const TOUR_STATE_EVENT_EXPORT_RETURN_MANUAL = 'export return manual';
    public const TOUR_STATE_EVENT_RETRY_EXPORT_RETURN = 'retry export return';
    public const TOUR_STATE_EVENT_NOTIFY_MERCHANT_RETURN = 'notify merchant return';
    public const TOUR_STATE_EVENT_END_JOURNEY = 'end journey';
    public const TOUR_STATE_EVENT_UNLOAD = 'unload';
    public const TOUR_STATE_EVENT_FINISH = 'finish';

    public const CONCRETE_TOUR_STATUS_ORDERABLE = 'orderable';
    public const CONCRETE_TOUR_STATUS_EMPTY = 'empty';
    public const CONCRETE_TOUR_STATUS_PLANABLE = 'planable';
    public const CONCRETE_TOUR_STATUS_DELIVERABLE = 'deliverable';
    public const CONCRETE_TOUR_STATUS_IN_DELIVERY = 'in delivery';
    public const CONCRETE_TOUR_VIRTUAL_STATUS_PLANABLE_TO_IN_DELIVERY = 'planable to in delivery';
    public const CONCRETE_TOUR_STATUS_DELIVERED = 'delivered';
}
