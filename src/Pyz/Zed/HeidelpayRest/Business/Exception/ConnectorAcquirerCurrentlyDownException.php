<?php
/**
 * Durst - project - ConnectorAcquirerCurrentlyDownException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-06
 * Time: 15:46
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;


use RuntimeException;

class ConnectorAcquirerCurrentlyDownException extends RuntimeException
{
    public const ERROR_CODE = 'COR.900.100.650';
    public const ERROR_CODE_ALTERNATIVE = 'COR.900.100.500';
}
