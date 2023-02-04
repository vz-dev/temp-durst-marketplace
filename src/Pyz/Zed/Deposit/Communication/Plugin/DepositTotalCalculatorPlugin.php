<?php
/**
 * Durst - project - DepositTotalCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.05.18
 * Time: 11:20
 */

namespace Pyz\Zed\Deposit\Communication\Plugin;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DepositTotalCalculator
 * @package Pyz\Zed\Deposit\Communication\Plugin
 * @method DepositFacadeInterface getFacade()
 */
class DepositTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateDepositTotal($calculableObjectTransfer);
    }
}