<?php

namespace Pyz\Zed\Sales\Communication\Exception;

use Exception;

class NoInvoiceForOrderException extends Exception
{
    public const MESSAGE = 'There is no invoice for order ID %d (yet)';
}
