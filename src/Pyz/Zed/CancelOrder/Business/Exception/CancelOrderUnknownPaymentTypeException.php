<?php
/**
 * Durst - project - CancelOrderUnknownPaymentTypeException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.09.21
 * Time: 14:48
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

class CancelOrderUnknownPaymentTypeException extends CancelOrderException
{
    public const MESSAGE = 'PaymentType "%s" ist unbekannt.';
}
