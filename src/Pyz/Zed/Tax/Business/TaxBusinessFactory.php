<?php
/**
 * Durst - project - TaxBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 22:03
 */

namespace Pyz\Zed\Tax\Business;

use Pyz\Zed\Tax\Business\Model\Calculator\TaxAmountAfterCancellationCalculator;
use Pyz\Zed\Tax\Business\Model\Calculator\TaxAmountCalculator;
use Pyz\Zed\Tax\Business\Model\TaxDefault;
use Spryker\Zed\Tax\Business\TaxBusinessFactory as SprykerTaxBusinessFactory;

/**
 * @method \Pyz\Zed\Tax\TaxConfig getConfig()
 */
class TaxBusinessFactory extends SprykerTaxBusinessFactory
{
    /**
     * @return \Pyz\Zed\Tax\Business\Model\Calculator\TaxAmountCalculator|\Spryker\Zed\Tax\Business\Model\Calculator\TaxAmountCalculator
     */
    public function createTaxAmountCalculator()
    {
        return new TaxAmountCalculator(
            $this->createAccruedTaxCalculator()
        );
    }

    /**
     * @return \Pyz\Zed\Tax\Business\Model\TaxDefault|\Spryker\Zed\Tax\Business\Model\TaxDefault
     */
    public function createTaxDefault()
    {
        return new TaxDefault(
            $this->getStore(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Tax\Business\Model\Calculator\TaxAmountAfterCancellationCalculator|\Spryker\Zed\Tax\Business\Model\Calculator\TaxAmountAfterCancellationCalculator
     */
    public function createTaxAmountAfterCancellationCalculator()
    {
        return new TaxAmountAfterCancellationCalculator(
            $this->createAccruedTaxCalculator()
        );
    }
}
