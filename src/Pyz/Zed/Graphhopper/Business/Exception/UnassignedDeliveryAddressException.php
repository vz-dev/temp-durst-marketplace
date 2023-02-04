<?php
/**
 * Durst - project - UnassignedDeliveryAddressException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.12.19
 * Time: 14:37
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class UnassignedDeliveryAddressException extends OptimizeException
{
    public const MESSAGE = 'Für die Bestellung %s konnte keine Optimierung durchgeführt werden.';
}
