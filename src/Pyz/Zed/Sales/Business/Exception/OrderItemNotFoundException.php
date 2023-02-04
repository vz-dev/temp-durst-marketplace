<?php

namespace Pyz\Zed\Sales\Business\Exception;

use RuntimeException;
use Throwable;

class OrderItemNotFoundException extends RuntimeException
{
    const MESSAGE = 'Order item with with ID %d not found';

    public function __construct(int $idSalesOrderItem, $message = self::MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf($message, $idSalesOrderItem), $code, $previous);
    }
}
