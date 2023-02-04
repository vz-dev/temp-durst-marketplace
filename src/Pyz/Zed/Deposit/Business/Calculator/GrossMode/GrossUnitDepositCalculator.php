<?php
/**
 * Durst - project - GrossUnitDepositCalculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 10:53
 */

namespace Pyz\Zed\Deposit\Business\Calculator\GrossMode;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class GrossUnitDepositCalculator implements CalculatorInterface
{
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        // do nothing, this method needs to be implemented as soon as gross and net mode are required
    }
}