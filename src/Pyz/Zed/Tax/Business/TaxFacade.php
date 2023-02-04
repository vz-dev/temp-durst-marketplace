<?php
/**
 * Durst - project - TaxFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 21:02
 */

namespace Pyz\Zed\Tax\Business;

use DateTime;
use Spryker\Zed\Tax\Business\TaxFacade as SprykerTaxFacade;

/**
 * Class TaxFacade
 * @package Pyz\Zed\Tax\Business
 * @method \Pyz\Zed\Tax\Business\TaxBusinessFactory getFactory()
 */
class TaxFacade extends SprykerTaxFacade implements TaxFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \DateTime $date
     *
     * @return float
     */
    public function getDefaultTaxRateForDate(DateTime $date)
    {
        return $this
            ->getFactory()
            ->createTaxDefault()
            ->getDefaultTaxRateForDate($date);
    }
}
