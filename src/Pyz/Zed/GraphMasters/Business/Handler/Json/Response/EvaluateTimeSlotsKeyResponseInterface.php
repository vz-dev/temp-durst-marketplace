<?php
/**
 * Durst - project - EvaluateTimeSlotsKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 23.06.21
 * Time: 11:30
 */

namespace Pyz\Zed\GraphMasters\Business\Handler\Json\Response;


class EvaluateTimeSlotsKeyResponseInterface
{
    public const EVALUTE_TIME_SLOTS_KEY_EVALUATION_RESULTS = 'evaluationResults';

    public const EVALUTE_TIME_SLOTS_KEY_TIME_SLOT = 'timeSlot';

    public const EVALUTE_TIME_SLOTS_KEY_START_TIME = 'startTime';
    public const EVALUTE_TIME_SLOTS_KEY_END_TIME = 'endTime';
    public const EVALUTE_TIME_SLOTS_KEY_IMPORTANCE = 'importance';
    public const EVALUTE_TIME_SLOTS_KEY_REASON = 'reason';

    public const EVALUTE_TIME_SLOTS_KEY_EVALUATION_SUCCEEDED = 'evaluationSucceeded';
    public const EVALUTE_TIME_SLOTS_KEY_NUM_OF_ORDERS = 'numberOfActualOrders';
    public const EVALUTE_TIME_SLOTS_KEY_NUM_OF_PREDICTED_ORDERS = 'numberOfPredictedOrders';
    public const EVALUTE_TIME_SLOTS_KEY_NUM_UNPERFROMED_ORDERS = 'numberOfUnperformedOrders';
    public const EVALUTE_TIME_SLOTS_KEY_TIME_SLOT_POSSIBLE = 'timeSlotPossible';
    public const EVALUTE_TIME_SLOTS_KEY_EXTRA_COST_DRIVING = 'costInExtraDrivingTimeMinutes';
    public const EVALUTE_TIME_SLOTS_KEY_EXTRA_WORK_TIME_MINUTES = 'extraWorkTimeMinutes';
    public const EVALUTE_TIME_SLOTS_KEY_EXTRA_DISTANCE_KILOMETER = 'extraDistanceKilometer';
    public const EVALUTE_TIME_SLOTS_KEY_TOUR_ID = 'tourId';
    public const EVALUTE_TIME_SLOTS_KEY_DRIVER_ID = 'driverId';
    public const EVALUTE_TIME_SLOTS_KEY_VEHICLE_ID = 'vehicleId';
    public const EVALUTE_TIME_SLOTS_KEY_ETA = 'eta';
    public const EVALUTE_TIME_SLOTS_KEY_ERROR = 'error';
}
