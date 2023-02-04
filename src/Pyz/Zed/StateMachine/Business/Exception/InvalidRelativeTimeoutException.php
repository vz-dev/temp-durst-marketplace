<?php
/**
 * Durst - project - InvalidRelativeTimeoutException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 18:44
 */

namespace Pyz\Zed\StateMachine\Business\Exception;

use RuntimeException;

class InvalidRelativeTimeoutException extends RuntimeException
{
    /**
     * @param string $timeout
     *
     * @return \Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException
     */
    public static function createNotValid(string $timeout)
    {
        return new InvalidRelativeTimeoutException(
            sprintf(
                'The string %s is not a valid relative timeout. It needs to consist of {table}.{column}',
                $timeout
            )
        );
    }

    /**
     * @param string $entityName
     *
     * @return \Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException
     */
    public static function createQueryNotFound(string $entityName)
    {
        return new InvalidRelativeTimeoutException(
            sprintf(
                'The query object %s does not exits',
                $entityName
            )
        );
    }

    /**
     * @param string $queryClass
     * @param int $pk
     *
     * @return \Pyz\Zed\StateMachine\Business\Exception\InvalidRelativeTimeoutException
     */
    public static function createEntityNotFound(string $queryClass, int $pk)
    {
        return new InvalidRelativeTimeoutException(
            sprintf(
                'The entity of query class %s with pk %d does not exits',
                $queryClass,
                $pk
            )
        );
    }
}
