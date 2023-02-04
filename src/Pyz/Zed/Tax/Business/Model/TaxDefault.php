<?php
/**
 * Durst - project - TaxDefault.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 08:55
 */

namespace Pyz\Zed\Tax\Business\Model;

use DateTime;
use Spryker\Zed\Tax\Business\Model\TaxDefault as SprykerTaxDefault;

/**
 * Class TaxDefault
 * @package Pyz\Zed\Tax\Business\Model
 * @property \Pyz\Zed\Tax\TaxConfig $config
 */
class TaxDefault extends SprykerTaxDefault
{
    /**
     * {@inheritDoc}
     *
     * @return float
     */
    public function getDefaultTaxRate()
    {
        return $this
            ->getDefaultTaxRateForDate(new DateTime('now'));
    }

    /**
     * @param \DateTime $date
     * @return float
     */
    public function getDefaultTaxRateForDate(DateTime $date)
    {
        $coronaTaxEnd = $this
            ->config
            ->getCoronaDeadlineEnd()
            ->setTime(23, 59, 59);

        if ($date >= $this->config->getCoronaDeadline()  && $date <= $coronaTaxEnd) {
            return $this->config->getCoronaTaxRate();
        }

        return parent::getDefaultTaxRate();
    }
}
