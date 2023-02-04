<?php
/**
 * Durst - project - TotalTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.05.19
 * Time: 08:23
 */

namespace Pyz\Zed\Calculation\Communication\Plugin;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class TotalTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
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
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $calculableObjectTransfer->setTotals(
                $calculableObjectTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getTotals());
        }
    }
}
