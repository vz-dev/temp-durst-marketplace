<?php
/**
 * Durst - project - TimeSlotDoesNotBelongToBranchException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-20
 * Time: 13:47
 */

namespace Pyz\Zed\DeliveryArea\Business\Exception;


use Exception;

class TimeSlotDoesNotBelongToBranchException extends Exception
{
    const MESSAGE = 'The timeslot with the id %d does not belong to the branch with id %d!';
}
