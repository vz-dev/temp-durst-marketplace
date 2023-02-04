<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Calculator;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Calculator\DeliveryCostTaxRateCalculator;
use Pyz\Zed\Tax\Business\TaxFacade;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;

class DeliveryCostTaxRateCalculatorTest extends AbstractDeliveryCostCalculatorTest
{
    public const TAX_RATE = 19.0;

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var TaxFacadeInterface|MockObject
     */
    protected $taxFacade;

    /**
     * @var DeliveryCostTaxRateCalculator
     */
    protected $deliveryCostTaxRateCalculator;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->taxFacade = $this->createTaxFacade();

        $this->deliveryCostTaxRateCalculator = new DeliveryCostTaxRateCalculator(
            $this->taxFacade
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _after()
    {
    }

    /**
     * @skip
     * @return void
     */
    public function testRecalculateSetsCorrectTaxRateInCorrectExpense()
    {
        $this->taxFacade->expects($this->atLeastOnce())
            ->method('getDefaultTaxRate')
            ->will($this->returnValue(static::TAX_RATE));

        $calculableObjectTransfer = $this->createCalculableObjectTransfer();

        $this
            ->deliveryCostTaxRateCalculator
            ->recalculate($calculableObjectTransfer);

        foreach ($this->getDeliveryCostExpenses($calculableObjectTransfer) as $deliveryCostExpense) {
            $this->assertEquals(
                static::TAX_RATE,
                $deliveryCostExpense->getTaxRate()
            );
        }
    }

    /**
     * @skip
     * @return void
     */
    public function testRecalculateDoesNotSetTaxRateForDummyExpense()
    {
        $this->taxFacade->expects($this->atLeastOnce())
            ->method('getDefaultTaxRate')
            ->will($this->returnValue(static::TAX_RATE));

        $calculableObjectTransfer = $this->createCalculableObjectTransfer();
        $calculableObjectTransfer->addExpense($this->createDummyExpense());

        $this
            ->deliveryCostTaxRateCalculator
            ->recalculate($calculableObjectTransfer);

        foreach ($calculableObjectTransfer->getExpenses() as $expenseTransfer) {
            if($expenseTransfer->getType() !== DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE){
                $this->assertNull($expenseTransfer->getTaxRate());
            }
        }
    }

    /**
     * @return MockObject|TaxFacadeInterface
     */
    protected function createTaxFacade()
    {
        $taxFacade = $this->getMockBuilder(TaxFacade::class)->setMethods(
            ['getDefaultTaxRate',]
        )->getMock();

        return $taxFacade;
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @return array|ExpenseTransfer[]
     */
    protected function getDeliveryCostExpenses(CalculableObjectTransfer $calculableObjectTransfer) : array
    {
        $deliveryCostExpenses = [];
        foreach ($calculableObjectTransfer->getExpenses() as $expense) {
            if($expense->getType() === DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE){
                $deliveryCostExpenses[] = $expense;
            }
        }

        return $deliveryCostExpenses;
    }
}
