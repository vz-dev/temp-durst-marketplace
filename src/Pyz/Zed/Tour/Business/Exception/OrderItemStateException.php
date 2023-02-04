<?php
/**
 * Durst - project - OrderItemStateException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 16.06.20
 * Time: 12:48
 */

namespace Pyz\Zed\Tour\Business\Exception;

use RuntimeException;

class OrderItemStateException extends RuntimeException
{
    protected const MESSAGE = 'The order item state id %d is not in the configured state whitelist.';

    /**
     * @param int $idOrderItemState
     *
     * @return static
     */
    public static function build(int $idOrderItemState): self
    {
        return new OrderItemStateException(
            sprintf(
                static::MESSAGE,
                $idOrderItemState
            )
        );
    }
}
