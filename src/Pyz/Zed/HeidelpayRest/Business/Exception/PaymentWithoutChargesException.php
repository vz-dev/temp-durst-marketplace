<?php
/**
 * Durst - project - PaymentWithoutChargesException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 16:10
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class PaymentWithoutChargesException extends RuntimeException
{
    protected const MESSAGE = 'The payment with the id %s does not have any charges';

    /**
     * @param string $paymentId
     *
     * @return static
     */
    public static function build(string $paymentId): self
    {
        return new PaymentWithoutChargesException(
            sprintf(
                static::MESSAGE,
                $paymentId
            )
        );
    }
}
