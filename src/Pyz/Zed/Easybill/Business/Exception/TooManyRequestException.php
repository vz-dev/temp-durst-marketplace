<?php
/**
 * Durst - project - TooManyRequestException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.01.20
 * Time: 15:02
 */

namespace Pyz\Zed\Easybill\Business\Exception;

class TooManyRequestException extends EasybillException
{
    protected const MESSAGE = 'too many requests to the api within the last minute';

    /**
     * @return static
     */
    public static function build(): self
    {
        return new TooManyRequestException(static::MESSAGE);
    }
}
