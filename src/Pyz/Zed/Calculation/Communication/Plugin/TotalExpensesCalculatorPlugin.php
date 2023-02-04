<?php
/**
 * Durst - project - TotalExpensesCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 08:33
 */

namespace Pyz\Zed\Calculation\Communication\Plugin;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class TotalExpensesCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     * @api
     *
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        if ($calculableObjectTransfer->getConcreteTimeSlots()->count() > 0) {
            $calculableObjectTransfer
                ->setExpenses(
                    $calculableObjectTransfer
                        ->getConcreteTimeSlots()
                        ->offsetGet(0)
                        ->getExpenses()
                );
        }
    }
}
