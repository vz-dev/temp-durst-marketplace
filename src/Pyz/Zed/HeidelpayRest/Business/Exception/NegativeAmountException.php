<?php
/**
 * Durst - project - NegativeAmountException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.02.19
 * Time: 18:29
 */

namespace Pyz\Zed\HeidelpayRest\Business\Exception;

use RuntimeException;

class NegativeAmountException extends RuntimeException
{
    public const MESSAGE = 'Cannot charge negative amount %d';
    public const REFUND_MESSAGE = 'Cannot refund negative amount %d (integer)';
    public const CANCELABLE = 'The cancelable amount %d for order with id %d is negative';

    /**
     * @param int $amount
     * @param int $idOrder
     * @return static
     */
    public static function cancelable(int $amount, int $idOrder): self{
        return new NegativeAmountException(
            sprintf(
                static::CANCELABLE,
                $amount,
                $idOrder
            )
        );
    }
}
