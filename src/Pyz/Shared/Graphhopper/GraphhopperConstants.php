<?php
/**
 * Durst - project - GraphhopperConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.11.19
 * Time: 11:56
 */

namespace Pyz\Shared\Graphhopper;


interface GraphhopperConstants
{
    public const GRAPHHOPPER_API_KEY = 'GRAPHHOPPER_API_KEY';
    public const GRAPHHOPPER_LOCALE = 'GRAPHHOPPER_LOCALE';

    //          Geocoding
    public const URL_GEOCODING = 'https://graphhopper.com/api/1/geocode';
    public const GRAPHHOPPER_GEOCODING_RESULT_LIMIT = 1;
    public const GRAPHHOPPER_GEOCODING_PROVIDER = 'GRAPHHOPPER_GEOCODING_PROVIDER';

    //          Optimize
    public const URL_OPTIMIZE = 'https://graphhopper.com/api/1/vrp/optimize';
    public const VEHICLE_TYPE_ID = 'sprinter';
//    public const VEHICLE_TYPE_PROFILE = 'small_truck';
    public const VEHICLE_TYPE_PROFILE = 'car';
    public const SERVICES_TYPE = 'delivery';
    public const SERVICES_DURATION = 120;
    public const OBJECTIVES_TYPE = 'min';
    public const OBJECTIVES_VALUE_VEHICLES = 'vehicles';
    public const OBJECTIVES_VALUE_COMPLETION_TIME = 'completion_time';

    //          Optimize Result
    public const URL_OPTIMIZE_RESULTS = 'https://graphhopper.com/api/1/vrp/solution/%s';
    public const STATUS_WAITING_IN_QUEUE = 'waiting_in_queue';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_FINISHED = 'finished';
}
