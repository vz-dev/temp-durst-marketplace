<?php
/**
 * Durst - project - GrossSumDepositCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 10:53
 */

namespace Pyz\Zed\Deposit\Business\Calculator\GrossMode;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class GrossSumDepositCalculator implements CalculatorInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        foreach($calculableObjectTransfer->getItems() as $itemTransfer){
            $itemTransfer->setSumDeposit($itemTransfer->getUnitDeposit()  *  $itemTransfer->getQuantity());
        }
    }
}