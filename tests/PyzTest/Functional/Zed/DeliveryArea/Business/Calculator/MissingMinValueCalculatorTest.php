<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Calculator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Pyz\Zed\DeliveryArea\Business\Calculator\MissingMinValueCalculator;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

class MissingMinValueCalculatorTest extends Unit
{
    public const GROSS_SUBTOTAL = 10;
    public const MIN_VALUE = 20;
    public const SUFFICIENT_GROSS_SUBTOTAL = 30;
    public const EXPECTED_MISSING_MIN_VALUE = 10;
    public const EXPECTED_MISSING_MIN_VALUE_ZERO = 0;

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var MissingMinValueCalculator
     */
    protected $missingMinValueCalculator;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->missingMinValueCalculator = new MissingMinValueCalculator();
    }

    /**
     * {@inheritdoc}
     */
    protected function _after()
    {
    }

    /**
     * @return void
     */
    public function testTransferAssertionsThrowExceptionForNoTotals()
    {
        $this->expectException(RequiredTransferPropertyException::class);

        $calculableObjectTransfer = $this->createCalculableObjectWithoutTotals();
        $calculableObjectTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setMinValue(0);

        $this
            ->missingMinValueCalculator
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * @return void
     */
    public function testTransferAssertionsThrowExceptionForNoMinValue()
    {
        $this->expectException(RequiredTransferPropertyException::class);

        $calculableObjectTransfer = $this->createCalculableObjectWithoutTotals();
        $calculableObjectTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setTotals(
                $this->createTotalsTransfer()
            );

        $this
            ->missingMinValueCalculator
            ->recalculate($this->createCalculableObjectWithoutTotals());
    }

    /**
     * @return void
     */
    public function testTransferAssertionsThrowExceptionForNoGrossSubtotal()
    {
        $this->expectException(RequiredTransferPropertyException::class);

        $calculableObjectTransfer = $this->createCalculableObjectWithoutTotals();
        $calculableObjectTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setTotals(
                $this->createTotalsTransfer()
            );

        $calculableObjectTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setMinValue(0);

        $this
            ->missingMinValueCalculator
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * @return void
     */
    public function testRecalculateCalculatesCorrectMissingMinValue()
    {
        $calculableObjectTransfer = $this->createCalculableObject(
            static::MIN_VALUE,
            static::GROSS_SUBTOTAL
        );

        $this
            ->missingMinValueCalculator
            ->recalculate($calculableObjectTransfer);

        $this
            ->assertEquals(
                static::EXPECTED_MISSING_MIN_VALUE,
                $calculableObjectTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getTotals()
                    ->getMissingMinAmountTotal()
            );
    }

    /**
     * @return void
     */
    public function testRecalculateCalculatesCorrectMissingMinValueForSufficientSubtotal()
    {
        $calculableObjectTransfer = $this->createCalculableObject(
            static::MIN_VALUE,
            static::SUFFICIENT_GROSS_SUBTOTAL
        );

        $this
            ->missingMinValueCalculator
            ->recalculate($calculableObjectTransfer);

        $this
            ->assertEquals(
                static::EXPECTED_MISSING_MIN_VALUE_ZERO,
                $calculableObjectTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getTotals()
                    ->getMissingMinAmountTotal()
            );
    }

    /**
     * @return void
     */
    public function testRecalculateCalculatesCorrectMissingMinValueForEqualValues()
    {
        $calculableObjectTransfer = $this->createCalculableObject(
            static::MIN_VALUE,
            static::MIN_VALUE
        );

        $this
            ->missingMinValueCalculator
            ->recalculate($calculableObjectTransfer);

        $this
            ->assertEquals(
                static::EXPECTED_MISSING_MIN_VALUE_ZERO,
                $calculableObjectTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getTotals()
                    ->getMissingMinAmountTotal()
            );
    }

    /**
     * @return TotalsTransfer
     */
    protected function createTotalsTransfer() : TotalsTransfer
    {
        return new TotalsTransfer();
    }

    /**
     * @param int $minValue
     * @param int $grossSubtotal
     * @return CalculableObjectTransfer
     */
    protected function createCalculableObject(int $minValue, int $grossSubtotal) : CalculableObjectTransfer
    {
        $totalsTransfer = $this->createTotalsTransfer();
        $totalsTransfer
            ->setGrossSubtotal($grossSubtotal);

        $calculableObject = $this->createCalculableObjectWithoutTotals();
        $calculableObject
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setMinValue($minValue)
            ->setTotals($totalsTransfer);

        return $calculableObject;

    }

    /**
     * @return CalculableObjectTransfer
     */
    protected function createCalculableObjectWithoutTotals() : CalculableObjectTransfer
    {
        return (new CalculableObjectTransfer())
            ->addConcreteTimeSlots(
                new ConcreteTimeSlotTransfer()
            );
    }
}
