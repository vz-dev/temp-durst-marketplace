<?php
/**
 * Durst - project - CancelOrderIssuerNotValidException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 09:13
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderIssuerNotValidException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderIssuerNotValidException extends CancelOrderException
{
    public const MESSAGE = 'Der Issuer "%s" ist nicht erlaubt.';
}
