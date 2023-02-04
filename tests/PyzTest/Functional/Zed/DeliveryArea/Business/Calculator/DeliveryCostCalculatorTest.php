<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Calculator;

use Pyz\Zed\DeliveryArea\Business\Calculator\DeliveryCostCalculator;

class DeliveryCostCalculatorTest extends AbstractDeliveryCostCalculatorTest
{
    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var DeliveryCostCalculator
     */
    protected $deliveryCostCalculator;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->deliveryCostCalculator = new DeliveryCostCalculator();
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
    public function testDeliveryCostTotalGetsCalculatedCorrectly()
    {
        $calculableObjectTransfer = $this->createCalculableObjectTransfer();

        $this
            ->deliveryCostCalculator
            ->recalculate($calculableObjectTransfer);

        $this->assertEquals(
            static::SUM_PRICE,
            $this->getDeliveryCostTotalFromCalculableObject($calculableObjectTransfer)
        );
    }

    /**
     * @return void
     */
    public function testDeliveryCostTotalOnlyCalculatesCorrectExpenseType()
    {
        $calculableObjectTransfer = $this->createCalculableObjectTransfer();
        $calculableObjectTransfer->addExpense($this->createDummyExpense());

        $this
            ->deliveryCostCalculator
            ->recalculate($calculableObjectTransfer);

        $this->assertEquals(
            static::SUM_PRICE,
            $this->getDeliveryCostTotalFromCalculableObject($calculableObjectTransfer)
        );
    }
}
