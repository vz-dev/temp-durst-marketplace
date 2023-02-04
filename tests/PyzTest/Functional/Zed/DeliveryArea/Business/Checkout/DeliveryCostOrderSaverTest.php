<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Checkout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderAddressTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\DeliveryArea\Business\Checkout\DeliveryCostOrderSaver;
use Pyz\Zed\Sales\Persistence\SalesQueryContainer;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group DeliveryArea
 * @group Checkout
 * @group DeliveryCostOrderSaverTest
 * Add your own group annotations below this line
 */
class DeliveryCostOrderSaverTest extends Unit
{
    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Checkout\DeliveryCostOrderSaver
     */
    protected $deliveryCostOrderSaver;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->deliveryCostOrderSaver = new DeliveryCostOrderSaver(new SalesQueryContainer());
    }

    /**
     * @return void
     */
    public function testDeliveryCostExpenseGetsAdded()
    {
        $quoteTransfer = $this->createQuoteTransfer();
        $saveOrderTransfer = $this->createSaveOrderTransfer(
            $this->setupOrder()
        );

        $this->deliveryCostOrderSaver->saveOrderShipment(
            $quoteTransfer,
            $saveOrderTransfer
        );

        $this->assertCount(1, $saveOrderTransfer->getOrderExpenses());

        $deliveryCostExpense = $saveOrderTransfer->getOrderExpenses()[0];

        $this->assertSame(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE, $deliveryCostExpense->getType());
        $this->assertSame(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_NAME, $deliveryCostExpense->getName());
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        $quoteTransfer = (new QuoteTransfer());

        $quoteTransfer->addExpense($this->createDeliveryCostExpense());
        $quoteTransfer->addExpense($this->createNotDeliveryCostExpense());

        return $quoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createDeliveryCostExpense(): ExpenseTransfer
    {
        return (new ExpenseTransfer())
            ->setQuantity(1)
            ->setType(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE)
            ->setIsNegative(false)
            ->setName(DeliveryAreaConstants::DELIVERY_COST_EXPENSE_NAME)
            ->setSumPrice(119)
            ->setSumGrossPrice(119)
            ->setSumTaxAmount(19)
            ->setSumDiscountAmountAggregation(0)
            ->setSumTaxAmount(19)
            ->setCanceledAmount(0)
            ->setRefundableAmount(119)
            ->setTaxRate(19.0)
            ->setTaxAmountAfterCancellation(0)
            ->setUnitGrossPrice(119)
            ->setUnitNetPrice(100)
            ->setUnitPrice(119)
            ->setUnitTaxAmount(19)
            ->setUnitDiscountAmountAggregation(0)
            ->setUnitPriceToPayAggregation(119);
    }

    /**
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createNotDeliveryCostExpense(): ExpenseTransfer
    {
        return (new ExpenseTransfer())
            ->setQuantity(1)
            ->setType('someExpenseTypeThatIsNotDeliveryCost')
            ->setIsNegative(false)
            ->setName('someExpenseNameThatIsNotDeliveryCost')
            ->setSumPrice(119)
            ->setSumGrossPrice(119)
            ->setSumTaxAmount(19)
            ->setSumDiscountAmountAggregation(0)
            ->setSumTaxAmount(19)
            ->setCanceledAmount(0)
            ->setRefundableAmount(119)
            ->setTaxRate(19.0)
            ->setTaxAmountAfterCancellation(0)
            ->setUnitGrossPrice(119)
            ->setUnitNetPrice(100)
            ->setUnitPrice(119)
            ->setUnitTaxAmount(19)
            ->setUnitDiscountAmountAggregation(0)
            ->setUnitPriceToPayAggregation(119);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    protected function createSaveOrderTransfer(int $idSalesOrder): SaveOrderTransfer
    {
        return (new SaveOrderTransfer())
            ->setIdSalesOrder($idSalesOrder);
    }

    /**
     *
     * @return int
     */
    protected function setupOrder(): int
    {
        $country = SpyCountryQuery::create()
            ->findOneByIso2Code('DE');

        $billingAddress = (new SpySalesOrderAddress())
            ->setSalutation(SpySalesOrderAddressTableMap::COL_SALUTATION_MR)
            ->setFirstName('Mathias')
            ->setLastName('Bicker')
            ->setEmail('mathias.bicker@durst.shop')
            ->setFkCountry($country->getIdCountry())
            ->setAddress1('Nußbaumerstr. 252')
            ->setAddress2('50825 Köln')
            ->setCity('Köln')
            ->setZipCode('50825');

        $billingAddress->save();

        $address = (new SpySalesOrderAddress())
            ->setSalutation(SpySalesOrderAddressTableMap::COL_SALUTATION_MR)
            ->setFirstName('Mathias')
            ->setLastName('Bicker')
            ->setEmail('mathias.bicker@durst.shop')
            ->setFkCountry($country->getIdCountry())
            ->setAddress1('Durststrecke GmbH')
            ->setAddress2('Oskar-Jäger-Straße 173 K4')
            ->setAddress3('50825 Köln')
            ->setCity('Köln')
            ->setZipCode('50825');

        $address->save();

        $order = (new SpySalesOrder())
            ->setEmail('mathias.bicker@durst.shop')
            ->setSalutation(SpySalesOrderTableMap::COL_SALUTATION_MR)
            ->setFirstName('Mathias')
            ->setLastName('Bicker')
            ->setFkSalesOrderAddressBilling($billingAddress->getIdSalesOrderAddress())
            ->setFkSalesOrderAddressShipping($address->getIdSalesOrderAddress())
            ->setFkBranch(8)
            ->setFkConcreteTimeslot(1)
            ->setOrderReference('TEST--1')
            ->setIsTest(true);

        $order->save();

        return $order->getIdSalesOrder();
    }
}
