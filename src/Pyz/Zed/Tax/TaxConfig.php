<?php
/**
 * Durst - project - TaxConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 08:56
 */

namespace Pyz\Zed\Tax;

use DateTime;
use Pyz\Shared\Tax\TaxConstants;
use Spryker\Zed\Tax\TaxConfig as SprykerTaxConfig;

class TaxConfig extends SprykerTaxConfig
{
    /**
     * @return \DateTime
     */
    public function getCoronaDeadline(): DateTime
    {
        return $this
            ->get(TaxConstants::TAX_CORONA_DEADLINE);
    }

    /**
     * @return \DateTime
     */
    public function getCoronaDeadlineEnd(): DateTime
    {
        return $this
            ->get(TaxConstants::TAX_CORONA_DEADLINE_END);
    }

    /**
     * @return float
     */
    public function getCoronaTaxRate(): float
    {
        return $this
            ->get(TaxConstants::TAX_CORONA_TAX_RATE);
    }
}
