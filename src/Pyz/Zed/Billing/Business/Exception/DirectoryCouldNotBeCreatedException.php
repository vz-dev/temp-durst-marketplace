<?php

namespace Pyz\Zed\Billing\Business\Exception;

use RuntimeException;

class DirectoryCouldNotBeCreatedException extends RuntimeException
{
    const MESSAGE = 'Could not create directory "%s"';
}
