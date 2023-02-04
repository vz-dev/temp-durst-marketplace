<?php
/**
 * Durst - project - DepositCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.04.18
 * Time: 17:32
 */

namespace Pyz\Zed\Deposit\Communication\Plugin;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DepositCalculatorPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin
 * @method DepositFacadeInterface getFacade()
 */
class DepositCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this->getFacade()
            ->calculateItemDeposit($calculableObjectTransfer);
    }
}