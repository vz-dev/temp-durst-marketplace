<?php
/**
 * Durst - project - WeightTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.09.18
 * Time: 11:44
 */

namespace Pyz\Zed\Deposit\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class WeightTotalCalculatorPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin\Calculation
 * @method DepositFacadeInterface getFacade()
 */
class WeightTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateWeightTotal($calculableObjectTransfer);
    }
}