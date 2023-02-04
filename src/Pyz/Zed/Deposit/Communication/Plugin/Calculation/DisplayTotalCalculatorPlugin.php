<?php
/**
 * Durst - project - DisplayTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.06.18
 * Time: 10:32
 */

namespace Pyz\Zed\Deposit\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DisplayTotalCalculatorPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin\Calculation
 * @method DepositFacadeInterface getFacade()
 */
class DisplayTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateDisplayTotal($calculableObjectTransfer);
    }
}