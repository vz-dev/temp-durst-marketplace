<?php
/**
 * Durst - project - GrossSubtotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.06.18
 * Time: 10:28
 */

namespace Pyz\Zed\MerchantPrice\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class GrossSubtotalCalculatorPlugin
 * @package Pyz\Zed\MerchantPrice\Communication\Plugin\Calculation
 * @method MerchantPriceFacadeInterface getFacade()
 */
class GrossSubtotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->recalculateGrossSubtotal($calculableObjectTransfer);
    }
}