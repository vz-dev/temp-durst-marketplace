<?php
/**
 * Durst - project - OptimizeKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.19
 * Time: 11:46
 */

namespace Pyz\Zed\Graphhopper\Business\Handler\Json\Request;

interface OptimizeKeyRequestInterface
{
    public const OPTIMIZE_API_KEY = GeocodingKeyRequestInterface::GEOCODING_API_KEY;

    public const VEHICLES = 'vehicles';
    public const VEHICLES_VEHICLE_ID = 'vehicle_id';
    public const VEHICLES_TYPE_ID = 'type_id';
    public const VEHICLES_START_ADDRESS = 'start_address';
    public const VEHICLES_START_ADDRESS_LOCATION_ID = 'location_id';
    public const VEHICLES_START_ADDRESS_LON = 'lon';
    public const VEHICLES_START_ADDRESS_LAT = 'lat';
    public const VEHICLES_START_ADDRESS_STREET_HINT = 'street_hint';
    public const VEHICLES_END_ADDRESS = 'end_address';
    public const VEHICLES_END_ADDRESS_LOCATION_ID = 'location_id';
    public const VEHICLES_END_ADDRESS_LON = 'lon';
    public const VEHICLES_END_ADDRESS_LAT = 'lat';
    public const VEHICLES_END_ADDRESS_STREET_HINT = 'street_hint';
    public const VEHICLES_EARLIEST_START = 'earliest_start';
    public const VEHICLES_RETURN_TO_DEPOT = 'return_to_depot';
    public const VEHICLES_LATEST_END = 'latest_end';
    public const VEHICLES_MAX_JOBS = 'max_jobs';

    public const VEHICLE_TYPES = 'vehicle_types';
    public const VEHICLE_TYPES_TYPE_ID = 'type_id';
    public const VEHICLE_TYPES_CAPACITY = 'capacity';
    public const VEHICLE_TYPES_PROFILE = 'profile';

    public const SERVICES = 'services';
    public const SERVICES_ID = 'id';
    public const SERVICES_NAME = 'name';
    public const SERVICES_ADDRESS = 'address';
    public const SERVICES_ADDRESS_LOCATION_ID = 'location_id';
    public const SERVICES_ADDRESS_LON = 'lon';
    public const SERVICES_ADDRESS_LAT = 'lat';
    public const SERVICES_ADDRESS_STREET_HINT = 'street_hint';
    public const SERVICES_TYPE = 'type';
    public const SERVICES_DURATION = 'duration';
    public const SERVICES_SIZE = 'size';
    public const SERVICES_TIME_WINDOWS = 'time_windows';
    public const SERVICES_TIME_WINDOWS_EARLIEST = 'earliest';
    public const SERVICES_TIME_WINDOWS_LATEST = 'latest';

    public const OBJECTIVES = 'objectives';
    public const OBJECTIVES_TYPE = 'type';
    public const OBJECTIVES_VALUE = 'value';

    public const CONFIGURATION = 'configuration';
    public const CONFIGURATION_ROUTING = 'routing';
    public const CONFIGURATION_ROUTING_CALC_POINTS = 'calc_points';
}
