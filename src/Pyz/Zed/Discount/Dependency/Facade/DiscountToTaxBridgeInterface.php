<?php
/**
 * Durst - project - DiscountToTaxBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.09.20
 * Time: 10:36
 */

namespace Pyz\Zed\Discount\Dependency\Facade;


use DateTime;

interface DiscountToTaxBridgeInterface
{
    /**
     * @param \DateTime $date
     * @return float
     */
    public function getDefaultTaxRateForDate(DateTime $date): float;
}
