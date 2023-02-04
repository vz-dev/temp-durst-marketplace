<?php
/**
 * Durst - project - OrderTaxRateTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.08.20
 * Time: 08:39
 */

namespace Pyz\Zed\Calculation\Communication\Plugin;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class OrderTaxRateTotalCalculatorPlugin
 * @package Pyz\Zed\Calculation\Communication\Plugin
 * @method \Pyz\Zed\Calculation\Business\CalculationFacadeInterface getFacade()
 */
class OrderTaxRateTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
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
            ->calculateOrderTaxRateTotal($calculableObjectTransfer);
    }
}
