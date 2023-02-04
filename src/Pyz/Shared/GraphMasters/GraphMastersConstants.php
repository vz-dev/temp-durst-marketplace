<?php
/**
 * Durst - project - GraphMastersConstants.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.05.21
 * Time: 19:49
 */

namespace Pyz\Shared\GraphMasters;


interface GraphMastersConstants
{
    public const GRAPHMASTERS_API_KEY = 'GRAPHMASTERS_API_KEY';
    public const GRAPHMASTERS_BASE_URL = 'GRAPHMASTERS_BASE_URL';

    public const GRAPHMASTERS_API_ENDPOINT_IMPORT_ORDER = 'api/v1/importOrder';
    public const GRAPHMASTERS_API_ENDPOINT_GET_TOURS = 'api/v1/getTours';
    public const GRAPHMASTERS_API_ENDPOINT_FIX_TOURS = 'api/v1/fixTours';
    public const GRAPHMASTERS_API_ENDPOINT_EVALUATE_TIME_SLOTS = 'api/v1/evaluateTimeSlots';

    public const GRAPHMASTERS_TIMESLOT_RESOURCE_TYPE = 'GM_TIME_SLOT';
    public const GRAPHMASTERS_SETTINGS_RESOURCE_TYPE = 'GM_SETTINGS';

    public const GRAPHMASTERS_DAYS_IN_ADVANCE = 'GRAPHMASTERS_DAYS_IN_ADVANCE';

    public const GRAPHMASTERS_TOUR_STATUS_CLOSED = 'closed';
    public const GRAPHMASTERS_TOUR_STATUS_IDLE = 'idle';
    public const GRAPHMASTERS_TOUR_STATUS_DOWNLOADED = 'downloaded';
    public const GRAPHMASTERS_TOUR_STATUS_IN_SERVICE = 'in_service';
    public const GRAPHMASTERS_TOUR_STATUS_RUNNING = 'running';
    public const GRAPHMASTERS_TOUR_STATUS_PAUSED = 'paused';
    public const GRAPHMASTERS_TOUR_STATUS_FINISHED = 'finished';

    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_ORDERABLE = 'orderable';
    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_PLANABLE = 'planable';
    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_IN_DELIVERY = 'in delivery';
    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_PLANABLE_TO_IN_DELIVERY = 'planable to in delivery';
    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_DELIVERED = 'delivered';
    public const GRAPHMASTERS_TOUR_VIRTUAL_STATUS_EMPTY = 'empty';

    public const GRAPHMASTERS_TOUR_FILTERING_EARLIEST_ALLOWED_DATE = 'GRAPHMASTERS_TOUR_FILTERING_EARLIEST_ALLOWED_DATE';

    public const GRAPHMASTERS_PREDICTED_ORDER_ID_PREFIX = 'predicted';

    public const GRAPHMASTERS_ACTION_TYPE_STOP = 'stop';

    public const GRAPHMASTERS_ORDER_STATUS_OPEN = 'open';
    public const GRAPHMASTERS_ORDER_STATUS_CANCELLED = 'cancelled';
    public const GRAPHMASTERS_ORDER_STATUS_CLOSED = 'closed';
    public const GRAPHMASTERS_ORDER_STATUS_PAUSED = 'paused';
    public const GRAPHMASTERS_ORDER_STATUS_FINISHED = 'finished';
}
