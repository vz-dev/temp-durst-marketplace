<?php
/**
 * Durst - project - DatabaseException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 09:10
 */

namespace Pyz\Zed\Integra\Business\Exception;

use RuntimeException;

class DatabaseException extends RuntimeException
{
    protected const UPDATE = 'unable to execute update query';

    /**
     * @return static
     */
    public static function update(): self
    {
        return new DatabaseException(static::UPDATE);
    }
}
