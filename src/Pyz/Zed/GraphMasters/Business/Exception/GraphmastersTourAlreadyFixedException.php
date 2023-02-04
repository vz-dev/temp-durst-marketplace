<?php

namespace Pyz\Zed\GraphMasters\Business\Exception;

use RuntimeException;
use Throwable;

class GraphmastersTourAlreadyFixedException extends RuntimeException
{
    public const DEFAULT_MESSAGE = 'Graphmasters tour with ID %d is already fixed';

    public function __construct(int $tourId, $message = self::DEFAULT_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($message, $tourId), $code, $previous);
    }
}
