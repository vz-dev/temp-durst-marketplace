<?php

namespace Pyz\Zed\GraphMasters\Business\Exception;

use RuntimeException;

class FailedRequestException extends RuntimeException
{
    protected const MESSAGE = 'Request failed. Code: %d, Message: %s';

    /**
     * @param int $code
     * @param string $errorMessage
     *
     * @return static
     */
    public static function build(int $code, string $errorMessage): self
    {
        return new static(
            sprintf(static::MESSAGE, $code, $errorMessage)
        );
    }
}
