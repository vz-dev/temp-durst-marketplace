<?php
/**
 * Durst - project - TaxRateTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.08.20
 * Time: 10:38
 */

namespace Pyz\Zed\Calculation\Communication\Plugin;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class TaxRateTotalCalculatorPlugin
 * @package Pyz\Zed\Calculation\Communication\Plugin
 * @method \Pyz\Zed\Calculation\Business\CalculationFacadeInterface getFacade()
 */
class TaxRateTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateTaxRateTotal($calculableObjectTransfer);
    }
}
