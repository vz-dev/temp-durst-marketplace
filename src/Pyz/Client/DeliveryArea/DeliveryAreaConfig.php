<?php
/**
 * Durst - project - DeliveryAreaConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.12.18
 * Time: 09:52
 */

namespace Pyz\Client\DeliveryArea;

use Spryker\Client\Kernel\AbstractBundleConfig;

class DeliveryAreaConfig extends AbstractBundleConfig
{
    /**
     * Sets the buffer that is added to the prep time to avoid customers beeing presented with
     * a time slot that is no longer available by the time they order
     *
     * @see http://php.net/manual/de/dateinterval.construct.php
     */
    public const CONCRETE_TIME_SLOT_QUERY_BUFFER = 'PT15M';
}
