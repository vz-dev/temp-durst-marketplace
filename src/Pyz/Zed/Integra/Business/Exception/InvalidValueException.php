<?php
/**
 * Durst - project - InvalidValueException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.11.20
 * Time: 10:55
 */

namespace Pyz\Zed\Integra\Business\Exception;

use RuntimeException;

class InvalidValueException extends RuntimeException
{
    protected const NEGATIVE = 'the value of %s is negative';
    protected const HEADER = 'unknown header column %s';
    protected const LEVEL = 'unknown log level %s';

    /**
     * @param string $valueName
     *
     * @return static
     */
    public static function negative(string $valueName): self
    {
        return new InvalidValueException(
            sprintf(
                static::NEGATIVE,
                $valueName
            )
        );
    }

    /**
     * @param string $headerColumn
     *
     * @return static
     */
    public static function header(string $headerColumn): self
    {
        return new InvalidValueException(
            sprintf(
                static::HEADER,
                $headerColumn
            )
        );
    }

    /**
     * @param string $level
     *
     * @return static
     */
    public static function level(string $level): self
    {
        return new InvalidValueException(
            sprintf(
                static::LEVEL,
                $level
            )
        );
    }
}
