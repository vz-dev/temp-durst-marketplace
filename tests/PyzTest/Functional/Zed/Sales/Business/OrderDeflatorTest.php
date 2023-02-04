<?php
/**
 * Durst - project - OrderDeflatorTest*
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-03
 * Time: 09:30
 */

namespace PyzTest\Functional\Zed\Sales\Business;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Pyz\Zed\Sales\Business\SalesFacade;

class OrderDeflatorTest extends Unit
{
    public const NO_OF_EXPENSE_1 = 5;
    public const NO_OF_EXPENSE_2 = 8;
    public const EXPECTED_NO_OF_EXPENSES = 2;

    public const EXPENSE_TYPE_PREFIX = 'deposit-';
    public const EXPENSE_1_SKU = '0987654321';
    public const EXPENSE_2_SKU = '1234567890';
    public const EXPENSE_1_PRICE = '330';
    public const EXPENSE_2_PRICE = '342';

    public const NO_ORDER_ITEMS_1 = 12;
    public const NO_ORDER_ITEMS_2 = 3;
    public const NO_ORDER_ITEMS_3 = 4;
    public const EXPECTED_NO_OF_ORDER_ITEMS = 3;

    public const ORDER_ITEMS_1_SKU = '9990987654321';
    public const ORDER_ITEMS_2_SKU = '9991234567890';
    public const ORDER_ITEMS_3_SKU = '9991234567891';

    public const ORDER_ITEMS_1_SUM = '1400';
    public const ORDER_ITEMS_2_SUM = '1500';
    public const ORDER_ITEMS_3_SUM = '1300';

    public const NO_REFUNDS_1 = 5;
    public const NO_REFUNDS_2 = 2;
    public const EXPECTED_NO_OF_REFUNDS = 2;

    public const REFUND_1_SKU = '0987654321888';
    public const REFUND_2_SKU = '1234567890888';
    public const REFUND_1_AMOUNT = '780';
    public const REFUND_2_AMOUNT = '1130';

    /**
     * @var \PyzTest\Functional\Zed\Sales\SalesBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var OrderTransfer
     */
    protected $orderTransfer;

    /**
     * @return void
     */
    protected function _before()
    {
        $this
            ->salesFacade = new SalesFacade();

        $this->orderTransfer = $this->createOrderTransfer();
    }

    /**
     * @return void
     */
    protected function _after()
    {
    }


    /**
     * @return void
     */
    public function testSalesOrderItemsDeflateToGroupedItemsQuantity()
    {
        $this
            ->assertEquals(
                static::NO_ORDER_ITEMS_1 + static::NO_ORDER_ITEMS_2 + static::NO_ORDER_ITEMS_3,
                $this->orderTransfer->getItems()->count()
            );

        $orderItems = $this->salesFacade->deflateSalesOrderItems($this->orderTransfer)->getItems();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_ORDER_ITEMS, $orderItems->count());

        foreach ($orderItems as $item)
        {
            if(strpos($item->getSku(), self::ORDER_ITEMS_1_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_1,
                        $item->getQuantity()
                    );
            }
            if(strpos($item->getSku(), self::ORDER_ITEMS_2_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_2,
                        $item->getQuantity()
                    );
            }
            if(strpos($item->getSku(), self::ORDER_ITEMS_3_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_3,
                        $item->getQuantity()
                    );
            }
        }
    }

    /**
     * @return void
     */
    public function testSalesOrderItemsDeflateToGroupedItemsSumAggregation()
    {
        $this
            ->assertEquals(
                static::NO_ORDER_ITEMS_1 + static::NO_ORDER_ITEMS_2 + static::NO_ORDER_ITEMS_3,
                $this->orderTransfer->getItems()->count()
            );

        $orderItems = $this->salesFacade->deflateSalesOrderItems($this->orderTransfer)->getItems();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_ORDER_ITEMS, $orderItems->count());

        foreach ($orderItems as $item)
        {
            if(strpos($item->getSku(), self::ORDER_ITEMS_1_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_1 * self::ORDER_ITEMS_1_SUM,
                        $item->getSumPriceToPayAggregation()
                    );
            }
            if(strpos($item->getSku(), self::ORDER_ITEMS_2_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_2 * self::ORDER_ITEMS_2_SUM,
                        $item->getSumPriceToPayAggregation()
                    );
            }
            if(strpos($item->getSku(), self::ORDER_ITEMS_3_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_ORDER_ITEMS_3 * self::ORDER_ITEMS_3_SUM,
                        $item->getSumPriceToPayAggregation()
                    );
            }
        }
    }

    /**
     * @return void
     */
    public function testSalesExpenseDeflateToGroupedExpensesQuantity()
    {
        $this
            ->assertEquals(
                self::NO_OF_EXPENSE_1 + self::NO_OF_EXPENSE_2,
                $this->orderTransfer->getExpenses()->count()
            );


        $orderSalesExpenses = $this->salesFacade->deflateSalesExpenses($this->createOrderTransfer())->getExpenses();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_EXPENSES, $orderSalesExpenses->count());

        foreach ($orderSalesExpenses as $expense)
        {
            if(strpos($expense->getType(), self::EXPENSE_1_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_OF_EXPENSE_1,
                        $expense->getQuantity()
                    );
            }
            if(strpos($expense->getType(), self::EXPENSE_2_SKU))
            {
                $this
                    ->assertEquals(
                        self::NO_OF_EXPENSE_2,
                        $expense->getQuantity()
                    );
            }
        }
    }

    /**
     * @return void
     */
    public function testSalesExpenseDeflateToGroupedExpensesPrice()
    {
        $orderSalesExpenses = $this->salesFacade->deflateSalesExpenses($this->createOrderTransfer())->getExpenses();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_EXPENSES, $orderSalesExpenses->count());

        foreach ($orderSalesExpenses as $expense)
        {
            if(strpos($expense->getType(), self::EXPENSE_1_SKU))
            {
                $this->assertEquals(
                    self::EXPENSE_1_PRICE * self::NO_OF_EXPENSE_1,
                    $expense->getSumPrice()
                );
            }
            if(strpos($expense->getType(), self::EXPENSE_2_SKU))
            {
                $this->assertEquals(
                    self::EXPENSE_2_PRICE * self::NO_OF_EXPENSE_2,
                    $expense->getSumPrice()
                );
            }
        }
    }

    /**
     * @return void
     */
    public function testSalesRefundDeflateToGroupedRefundQuantity()
    {

        $this
            ->assertEquals(
                self::NO_REFUNDS_1 + self::NO_REFUNDS_2,
                $this->orderTransfer->getRefunds()->count()
            );

        $salesRefunds = $this->salesFacade->deflateSalesRefunds($this->createOrderTransfer())->getRefunds();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_REFUNDS, $salesRefunds->count());

        foreach ($salesRefunds as $refund)
        {
            if(strpos($refund->getSku(), self::REFUND_1_SKU))
            {
                $this->assertEquals(
                    self::NO_REFUNDS_1,
                    $refund->getQuantity()
                );
            }
            if(strpos($refund->getSku(), self::REFUND_2_SKU))
            {
                $this->assertEquals(
                    self::NO_REFUNDS_2,
                    $refund->getQuantity()
                );
            }

        }
    }

    /**
     * @return void
     */
    public function testSalesRefundDeflateToGroupedRefundAmount()
    {
        $salesRefunds = $this->salesFacade->deflateSalesRefunds($this->createOrderTransfer())->getRefunds();

        $this
            ->assertEquals(static::EXPECTED_NO_OF_REFUNDS, $salesRefunds->count());

        foreach ($salesRefunds as $refund)
        {
            if(strpos($refund->getSku(), self::REFUND_1_SKU))
            {
                $this->assertEquals(
                    self::REFUND_1_AMOUNT * self::NO_REFUNDS_1,
                    $refund->getAmount()
                );
            }
            if(strpos($refund->getSku(), self::REFUND_2_SKU))
            {
                $this->assertEquals(
                    self::REFUND_2_AMOUNT * self::NO_REFUNDS_2,
                    $refund->getAmount()
                );
            }

        }
    }


    /**
     * @return OrderTransfer
     */
    protected function createOrderTransfer() : OrderTransfer
    {
        $orderTransfer = new OrderTransfer();

        for($i=0; $i < self::NO_OF_EXPENSE_1; $i++){
            $expenseTransfer = new ExpenseTransfer();
            $expenseTransfer->setQuantity(1);
            $expenseTransfer->setType(self::EXPENSE_TYPE_PREFIX.self::EXPENSE_1_SKU.'-'.$i);
            $expenseTransfer->setSumPrice(self::EXPENSE_1_PRICE);

            $orderTransfer->addExpense($expenseTransfer);
        }

        for($i=0; $i < self::NO_OF_EXPENSE_2; $i++){
            $expenseTransfer = new ExpenseTransfer();
            $expenseTransfer->setQuantity(1);
            $expenseTransfer->setType(self::EXPENSE_TYPE_PREFIX.self::EXPENSE_2_SKU.'-'.$i);
            $expenseTransfer->setSumPrice(self::EXPENSE_2_PRICE);

            $orderTransfer->addExpense($expenseTransfer);
        }

        for($i=0; $i < self::NO_ORDER_ITEMS_1; $i++)
        {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->setQuantity(1);
            $itemTransfer->setSku(self::ORDER_ITEMS_1_SKU);
            $itemTransfer->setSumPriceToPayAggregation(self::ORDER_ITEMS_1_SUM);

            $orderTransfer->addItem($itemTransfer);
        }

        for($i=0; $i < self::NO_ORDER_ITEMS_2; $i++)
        {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->setQuantity(1);
            $itemTransfer->setSku(self::ORDER_ITEMS_2_SKU);
            $itemTransfer->setSumPriceToPayAggregation(self::ORDER_ITEMS_2_SUM);

            $orderTransfer->addItem($itemTransfer);
        }

        for($i=0; $i < self::NO_ORDER_ITEMS_3; $i++)
        {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->setQuantity(1);
            $itemTransfer->setSku(self::ORDER_ITEMS_3_SKU);
            $itemTransfer->setSumPriceToPayAggregation(self::ORDER_ITEMS_3_SUM);

            $orderTransfer->addItem($itemTransfer);
        }

        for($i=0; $i < self::NO_REFUNDS_1; $i++)
        {
            $refundTransfer = new RefundTransfer();
            $refundTransfer->setQuantity(1);
            $refundTransfer->setSku(self::REFUND_1_SKU);

            $orderTransfer->addRefund($refundTransfer);
        }

        for($i=0; $i < self::NO_REFUNDS_2; $i++)
        {
            $refundTransfer = new RefundTransfer();
            $refundTransfer->setQuantity(1);
            $refundTransfer->setSku(self::REFUND_2_SKU);

            $orderTransfer->addRefund($refundTransfer);
        }

        return $orderTransfer;
    }



}
