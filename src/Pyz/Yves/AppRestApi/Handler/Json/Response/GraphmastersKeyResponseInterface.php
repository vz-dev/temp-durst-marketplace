<?php
/**
 * Durst - project - GraphmastersResponseInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 20:45
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;


interface GraphmastersKeyResponseInterface
{
    public const KEY_TIME_SLOTS = 'time_slots';

    public const EVALUATE_TIME_SLOTS_KEY_TIME_SLOT = 'time_slot';

    public const EVALUATE_TIME_SLOTS_KEY_START_TIME = 'start_time';
    public const EVALUATE_TIME_SLOTS_KEY_END_TIME = 'end_time';
    public const EVALUATE_TIME_SLOTS_KEY_IMPORTANCE = 'importance';
    public const EVALUATE_TIME_SLOTS_KEY_REASON = 'reason';

    public const EVALUATE_TIME_SLOTS_KEY_EVALUATION_SUCCEEDED = 'evaluation_succeeded';
    public const EVALUATE_TIME_SLOTS_KEY_TIME_SLOT_POSSIBLE = 'time_slot_possible';
    public const EVALUATE_TIME_SLOTS_KEY_EXTRA_COST_DRIVING = 'cost_in_extra_driving_time_minutes';
    public const EVALUATE_TIME_SLOTS_KEY_EXTRA_WORK_TIME_MINS = 'extra_work_time_mins';
    public const EVALUATE_TIME_SLOTS_KEY_EXTRA_DISTANCE_KILOMETERS = 'extra_distance_kilometer';
    public const EVALUATE_TIME_SLOTS_KEY_ETA = 'eta';

    public const EVALUATE_TIME_SLOTS_KEY_ORIG_START_TIME = 'orig_start_time';
    public const EVALUATE_TIME_SLOTS_KEY_ORIG_END_TIME = 'orig_end_time';
    public const EVALUATE_TIME_SLOTS_TOUR_ID = 'tour_id';
    public const EVALUATE_TIME_SLOTS_DRIVER_ID = 'driver_id';
    public const EVALUATE_TIME_SLOTS_VEHICLE_ID = 'vehicle_id';
    public const EVALUATE_TIME_SLOTS_KEY_NUM_OF_ORDERS = 'number_of_actual_orders';
    public const EVALUATE_TIME_SLOTS_KEY_NUM_OF_PREDICTED_ORDERS = 'number_of_predicted_orders';
    public const EVALUATE_TIME_SLOTS_KEY_NUM_UNPERFROMED_ORDERS = 'number_of_unperformed_orders';

    public const EVALUATE_TIME_SLOTS_KEY_ERROR = 'error';
    public const EVALUATE_TIME_SLOTS_KEY_ERROR_CODE = 'error_code';
}
