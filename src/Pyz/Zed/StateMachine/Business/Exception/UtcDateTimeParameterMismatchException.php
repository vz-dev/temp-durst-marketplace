<?php
/**
 * Durst - project - UtcDateTimeParameterMismatchException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\StateMachine\Business\Exception;


use RuntimeException;

class UtcDateTimeParameterMismatchException extends RuntimeException
{

    /**
     * @param string $utcDateTime
     * @return \Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException
     */
    public static function createParameterIsMissing(string $utcDateTime): self
    {
        return new UtcDateTimeParameterMismatchException(
            sprintf(
                '%s is not a valid UTC datetime. It needs to consist of {table}.{column}',
                $utcDateTime
            )
        );
    }

    /**
     * @param string $queryName
     * @return \Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException
     */
    public static function createQueryClassMissing(string $queryName): self
    {
        return new UtcDateTimeParameterMismatchException(
            sprintf(
                'The query class %s does not exist.',
                $queryName
            )
        );
    }

    /**
     * @param int $identifier
     * @return \Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException
     */
    public static function createEntityNotFound(int $identifier): self
    {
        return new UtcDateTimeParameterMismatchException(
            sprintf(
                'Could not find entity with identifier %d',
                $identifier
            )
        );
    }

    /**
     * @param mixed $value
     * @return \Pyz\Zed\StateMachine\Business\Exception\UtcDateTimeParameterMismatchException
     */
    public static function createReturnValueNotADateTime($value): self
    {
        if (is_object($value) === true) {
            $returnType = get_class($value);
        } else {
            $returnType = $value;
        }

        return new UtcDateTimeParameterMismatchException(
            sprintf(
                'The returned value %s is not a DateTime object.',
                $returnType
            )
        );
    }
}
