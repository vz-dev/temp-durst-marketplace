<?php
/**
 * Durst - project - CancelOrderConcreteTourNotFoundException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 10:34
 */

namespace Pyz\Zed\CancelOrder\Business\Exception;

/**
 * Class CancelOrderConcreteTourNotFoundException
 * @package Pyz\Zed\CancelOrder\Business\Exception
 */
class CancelOrderConcreteTourNotFoundException extends CancelOrderException
{
    public const MESSAGE = 'Die Tour mit der ID %d wurde nicht gefunden.';
}
