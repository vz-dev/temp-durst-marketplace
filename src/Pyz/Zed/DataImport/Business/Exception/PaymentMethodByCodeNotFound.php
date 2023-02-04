<?php
/**
 * Durst - project - PaymentMethodByCodeNotFound.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.09.18
 * Time: 11:00
 */

namespace Pyz\Zed\DataImport\Business\Exception;


class PaymentMethodByCodeNotFound extends \RuntimeException
{
    public const MESSAGE = 'Payment method with code %s could not be found';
}