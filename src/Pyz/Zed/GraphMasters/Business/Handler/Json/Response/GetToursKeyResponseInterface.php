<?php

namespace Pyz\Zed\GraphMasters\Business\Handler\Json\Response;


class GetToursKeyResponseInterface
{
    public const TOURS = 'tours';
    public const UNASSIGNED_ORDER_IDS = 'unassignedOrderIds';

    public const TOUR_ID = 'id';
    public const TOUR_SHIFT_START = 'shiftStart';
    public const TOUR_NAME = 'name';
    public const TOUR_VEHICLE_ID = 'vehicleId';
    public const TOUR_DRIVER_ID = 'driverId';
    public const TOUR_START_LOCATION = 'startLocation'; // GeoLocation
    public const TOUR_DESTINATION_LOCATION = 'destinationLocation'; // GeoLocation
    public const TOUR_START_ETA = 'tourStartEta';
    public const TOUR_DESTINATION_ETA = 'tourDestinationEta';
    public const TOUR_STATUS = 'tourStatus';
    public const TOUR_VEHICLE_STATUS = 'vehicleStatus';
    public const TOUR_TOTAL_DISTANCE_METERS = 'totalDistanceMeters';
    public const TOUR_TOTAL_TIME_SECONDS = 'totalTimeSeconds';
    public const TOUR_OPEN_ACTIONS = 'openActions'; // Action[]
    public const TOUR_FINISHED_ACTIONS = 'finishedActions'; // Action[]
    public const TOUR_SUSPENDED_ORDER_IDS = 'suspendedOrderIds';
    public const TOUR_UNPERFORMED_ORDER_IDS = 'unperformedOrderIds';

    public const GEO_LOCATION_LAT = 'lat';
    public const GEO_LOCATION_LNG = 'lng';

    public const ACTION_TYPE = 'actionType';
    public const ACTION_START_TIME = 'startTime';
    public const ACTION_ORDER_IDS = 'orderIds';
    public const ACTION_LOCATION = 'location';
    public const ACTION_DISTANCE_METERS = 'distanceMeters';
}
