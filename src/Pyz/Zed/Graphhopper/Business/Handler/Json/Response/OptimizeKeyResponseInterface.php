<?php
/**
 * Durst - project - OptimizeKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.19
 * Time: 15:07
 */

namespace Pyz\Zed\Graphhopper\Business\Handler\Json\Response;


interface OptimizeKeyResponseInterface
{
    public const JOB_ID = 'job_id';

    public const COPYRIGHTS = 'copyrights';

    public const STATUS = 'status';

    public const WAITING_TIME_IN_QUEUE = 'waiting_time_in_queue';

    public const PROCESSING_TIME = 'processing_time';

    public const SOLUTION = 'solution';
    public const SOLUTION_COSTS = 'costs';
    public const SOLUTION_DISTANCE = 'distance';
    public const SOLUTION_TIME = 'time';
    public const SOLUTION_TRANSPORT_TIME = 'transport_time';
    public const SOLUTION_COMPLETION_TIME = 'completion_time';
    public const SOLUTION_MAX_OPERATION_TIME = 'max_operation_time';
    public const SOLUTION_WAITING_TIME = 'waiting_time';
    public const SOLUTION_SERVICE_DURATION = 'service_duration';
    public const SOLUTION_PREPARATION_TIME = 'preparation_time';
    public const SOLUTION_NO_VEHICLES = 'no_vehicles';
    public const SOLUTION_NO_UNASSIGNED = 'no_unassigned';
    public const SOLUTION_ROUTES = 'routes';
    public const SOLUTION_ROUTES_VEHICLE_ID = 'vehicle_id';
    public const SOLUTION_ROUTES_DISTANCE = 'distance';
    public const SOLUTION_ROUTES_TRANSPORT_TIME = 'transport_time';
    public const SOLUTION_ROUTES_COMPLETION_TIME = 'completion_time';
    public const SOLUTION_ROUTES_WAITING_TIME = 'waiting_time';
    public const SOLUTION_ROUTES_SERVICE_DURATION = 'service_duration';
    public const SOLUTION_ROUTES_PREPARATION_TIME = 'preparation_time';
    public const SOLUTION_ROUTES_POINTS = 'points';
    public const SOLUTION_ROUTES_POINTS_COORDINATES = 'coordinates';
    public const SOLUTION_ROUTES_POINTS_TYPE = 'type';
    public const SOLUTION_ROUTES_ACTIVITIES = 'activities';
    public const SOLUTION_ROUTES_ACTIVITIES_TYPE = 'type';
    public const SOLUTION_ROUTES_ACTIVITIES_ID = 'id';
    public const SOLUTION_ROUTES_ACTIVITIES_LOCATION_ID = 'location_id';
    public const SOLUTION_ROUTES_ACTIVITIES_ADDRESS = 'address';
    public const SOLUTION_ROUTES_ACTIVITIES_ADDRESS_LOCATION_ID = 'location_id';
    public const SOLUTION_ROUTES_ACTIVITIES_ADDRESS_LAT = 'lat';
    public const SOLUTION_ROUTES_ACTIVITIES_ADDRESS_LON = 'lon';
    public const SOLUTION_ROUTES_ACTIVITIES_ARR_TIME = 'arr_time';
    public const SOLUTION_ROUTES_ACTIVITIES_ARR_DATE_TIME = 'arr_date_time';
    public const SOLUTION_ROUTES_ACTIVITIES_END_TIME = 'end_time';
    public const SOLUTION_ROUTES_ACTIVITIES_END_DATE_TIME = 'end_date_time';
    public const SOLUTION_ROUTES_ACTIVITIES_DISTANCE = 'distance';
    public const SOLUTION_ROUTES_ACTIVITIES_DRIVING_TIME = 'driving_time';
    public const SOLUTION_ROUTES_ACTIVITIES_PREPARATION_TIME = 'preparation_time';
    public const SOLUTION_ROUTES_ACTIVITIES_WAITING_TIME = 'waiting_time';
    public const SOLUTION_ROUTES_ACTIVITIES_LOAD_BEFORE = 'load_before';
    public const SOLUTION_ROUTES_ACTIVITIES_LOAD_AFTER = 'load_after';
    public const SOLUTION_UNASSIGNED = 'unassigned';
    public const SOLUTION_UNASSIGNED_SERVICES = 'services';
    public const SOLUTION_UNASSIGNED_SHIPMENTS = 'shipments';
    public const SOLUTION_UNASSIGNED_BREAKS = 'breaks';
    public const SOLUTION_UNASSIGNED_DETAILS = 'details';
}
