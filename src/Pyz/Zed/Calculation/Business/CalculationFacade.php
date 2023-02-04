<?php
/**
 * Durst - project - CalculationFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.08.20
 * Time: 10:35
 */

namespace Pyz\Zed\Calculation\Business;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\CalculationFacade as SprykerCalculationFacade;

/**
 * Class CalculationFacade
 * @package Pyz\Zed\Calculation\Business
 * @method \Pyz\Zed\Calculation\Business\CalculationBusinessFactory getFactory()
 */
class CalculationFacade extends SprykerCalculationFacade implements CalculationFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateTaxRateTotal(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $this
            ->getFactory()
            ->createTaxRateTotalCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateOrderTaxRateTotal(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $this
            ->getFactory()
            ->createOrderTaxRateTotalCalculator()
            ->recalculate($calculableObjectTransfer);
    }
}
