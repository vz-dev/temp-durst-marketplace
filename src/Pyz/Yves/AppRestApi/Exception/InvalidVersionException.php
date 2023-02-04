<?php

namespace Pyz\Yves\AppRestApi\Exception;

use RuntimeException;

class InvalidVersionException extends RuntimeException
{
    const MESSAGE = 'The specified version "%s" is not valid.';
}
