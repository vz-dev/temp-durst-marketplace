<?php
/**
 * Durst - project - Calculator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.04.18
 * Time: 17:36
 */

namespace Pyz\Zed\Deposit\Business\Calculator;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class Calculator implements CalculatorInterface
{

    /**
     * @var CalculatorInterface[]
     */
    protected $calculators;

    /**
     * Calculator constructor.
     * @param CalculatorInterface[] $calculators
     */
    public function __construct(array $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        foreach ($this->calculators as $calculator) {
            $calculator->recalculate($calculableObjectTransfer);
        }
    }

}