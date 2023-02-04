<?php
/**
 * Durst - project - TaxFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 21:01
 */

namespace Pyz\Zed\Tax\Business;

use DateTime;
use Spryker\Zed\Tax\Business\TaxFacadeInterface as SprykerTaxFacadeInterface;

interface TaxFacadeInterface extends SprykerTaxFacadeInterface
{
    /**
     * Specification:
     *  - returns the default tax that is valid at the given time
     *  - if the given date is after the configured deadline
     * @link \Pyz\Shared\Tax\TaxConstants::TAX_CORONA_DEADLINE
     *    the configured special tax rate
     * @link \Pyz\Shared\Tax\TaxConstants::TAX_CORONA_TAX_RATE
     *    is returned
     *  - otherwise the default tax rate is returned
     *
     * @param \DateTime $date
     *
     * @return float
     */
    public function getDefaultTaxRateForDate(DateTime $date);
}
