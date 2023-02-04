<?php
/**
 * Durst - project - IndividualTotalsPreperationPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-13
 * Time: 08:39
 */

namespace Pyz\Zed\Calculation\Communication\Plugin;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class IndividualTotalsPreparationPlugin extends AbstractPlugin implements CalculationPluginInterface
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
            $expenses = $calculableObjectTransfer
                ->requireExpenses()
                ->getExpenses();
            $totals = $calculableObjectTransfer
                ->requireTotals()
                ->getTotals();

            foreach ($calculableObjectTransfer->getConcreteTimeSlots() as $concreteTimeSlot) {
                $concreteTimeSlot
                    ->setTotals(clone $totals);

                foreach ($expenses as $expense) {
                    $concreteTimeSlot
                        ->addExpenses($expense);
                }
            }
        }
    }
}
