<?php
/**
 * Durst - project - SalutationNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 10:29
 */

namespace Pyz\Zed\Merchant\Business\Exception;

use Exception;

class SalutationNotFoundException extends Exception
{
    public const ID_NOT_FOUND = 'The salutation with the id %d does not exist in the database';
}
