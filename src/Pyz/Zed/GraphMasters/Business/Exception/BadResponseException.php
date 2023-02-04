<?php

namespace Pyz\Zed\GraphMasters\Business\Exception;

use RuntimeException;

class BadResponseException extends RuntimeException
{
    protected const MESSAGE = 'Received bad response. Code: %d Message: %s';

    /**
     * @param int $code
     * @param string $codeMessage
     *
     * @return static
     */
    public static function build(int $code, string $codeMessage): self
    {
        return new static(
            sprintf(static::MESSAGE, $code, $codeMessage)
        );
    }
}
