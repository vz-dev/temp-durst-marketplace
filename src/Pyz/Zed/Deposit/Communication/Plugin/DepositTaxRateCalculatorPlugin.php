<?php
/**
 * Durst - project - DepositTaxRateCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.06.18
 * Time: 14:50
 */

namespace Pyz\Zed\Deposit\Communication\Plugin;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DepositTaxRateCalculatorPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin
 * @method DepositFacadeInterface getFacade()
 */
class DepositTaxRateCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateDepositTaxRate($calculableObjectTransfer);
    }
}