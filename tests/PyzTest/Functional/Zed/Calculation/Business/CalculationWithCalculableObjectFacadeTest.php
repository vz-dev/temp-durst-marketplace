<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Functional\Zed\Calculation\Business;

use Codeception\TestCase\Test;
use DateTime;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Currency\Persistence\SpyCurrencyQuery;
use Orm\Zed\Discount\Persistence\Base\SpyDiscountQuery;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Orm\Zed\Discount\Persistence\SpyDiscountAmount;
use Orm\Zed\Discount\Persistence\SpyDiscountVoucher;
use Orm\Zed\Discount\Persistence\SpyDiscountVoucherPool;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderAddressTableMap;
use Orm\Zed\Tax\Persistence\SpyTaxRate;
use Orm\Zed\Tax\Persistence\SpyTaxSet;
use Orm\Zed\Tax\Persistence\SpyTaxSetTax;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Deposit\DepositConstants;
use Spryker\Shared\Calculation\CalculationPriceMode;
use Spryker\Shared\Tax\TaxConstants;
use Spryker\Zed\Calculation\Business\CalculationFacade;
use Spryker\Zed\Discount\DiscountDependencyProvider;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Calculation
 * @group Business
 * @group Facade
 * @group CalculationWithCalculableObjectFacadeTest
 * Add your own group annotations below this line
 */
class CalculationWithCalculableObjectFacadeTest extends Test
{
    protected const ITEM_SKU = '180412008009146';

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->resetCurrentDiscounts();
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 2x Reissdorf Kölsch          12.61       25.22       15.00       30.00
     *      MwSt (19%)               2.39        4.78
     *
     * 2x Pfand 24x 0.33l            2.87        5.74        3.42        6.84
     *      MwSt (19%)               0.55        1.10
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt (19%)               0.48        0.48
     *______________________________________________________________________________
     *
     *      Net Total                                                   33.48
     *      Display Total                                               33.00
     *      Deposit Total                                                6.84
     *      inkl. MwSt                                                   6.36
     *      Gross Total                                                 39.84
     *
     * @return void
     */
    public function testCalculatorStackWithGrossPriceMode()
    {
        $calculationFacade = $this->createCalculationFacade();

        $quoteTransfer = $this->createFixtureDataForCalculation();
        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        $itemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(19, $itemTransfer->getTaxRate());
        $this->assertSame(239, $itemTransfer->getUnitTaxAmount());
        $this->assertSame(479, $itemTransfer->getSumTaxAmount());
        $this->assertSame(239, $itemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(479, $itemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(1500, $itemTransfer->getUnitGrossPrice());
        $this->assertSame(3000, $itemTransfer->getSumGrossPrice());

        $this->assertSame(1500, $itemTransfer->getUnitPrice());
        $this->assertSame(3000, $itemTransfer->getSumPrice());

        $this->assertSame(1500, $itemTransfer->getUnitSubtotalAggregation());
        $this->assertSame(3000, $itemTransfer->getSumSubtotalAggregation());

        $this->assertSame(1500, $itemTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(3000, $itemTransfer->getSumPriceToPayAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountFullAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountFullAggregation());

        $depositExpenseTransfer = $recalculatedQuoteTransfer
            ->getExpenses()[0];

        $this->assertSame(342, $depositExpenseTransfer->getUnitGrossPrice());
        $this->assertSame(684, $depositExpenseTransfer->getSumGrossPrice());

        $this->assertSame(342, $depositExpenseTransfer->getUnitPrice());
        $this->assertSame(684, $depositExpenseTransfer->getSumPrice());

        $this->assertSame(342, $depositExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(684, $depositExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $depositExpenseTransfer->getTaxRate());
        $this->assertSame(55, $depositExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(109, $depositExpenseTransfer->getSumTaxAmount());

        $deliveryCostExpenseTransfer = $recalculatedQuoteTransfer->getExpenses()[1];

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitGrossPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumGrossPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $deliveryCostExpenseTransfer->getTaxRate());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getSumTaxAmount());

        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();
        $this->assertSame(3000, $totalsTransfer->getSubtotal());
        $this->assertSame(0, $totalsTransfer->getDiscountTotal());
        $this->assertSame(984, $totalsTransfer->getExpenseTotal());
        $this->assertSame(3984, $totalsTransfer->getGrandTotal());
        $this->assertSame(636, $totalsTransfer->getTaxTotal()->getAmount());
        $this->assertSame(3300, $totalsTransfer->getDisplayTotal());
        $this->assertSame(684, $totalsTransfer->getDepositTotal());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 2x Reissdorf Kölsch          12.61       25.22       15.00       30.00
     *      MwSt (19%)               2.39        4.78
     *
     * 2x Voucher Code Rabatt       -0.17       -0.34       -0.20       -0.40
     *      MwSt (19%)              -0.03       -0.06
     *
     * 2x Pfand 24x 0.33l            2.87        5.74        3.42        6.84
     *      MwSt (19%)               0.55        1.10
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt (19%)               0.48        0.48
     *
     *______________________________________________________________________________
     *
     *      Net Total                                                   33.14
     *      Display Total                                               32.60
     *      Discount Total                                              -0.40
     *      Deposit Total                                                6.84
     *      inkl. MwSt                                                   6.30
     *      Gross Total                                                 39.44
     *
     * @return void
     */
    public function testCalculatorStackWithGrossPriceModeAfterDiscounts()
    {
        $calculationFacade = $this->createCalculationFacade();

        $discountAmount = 20;
        $quoteTransfer = $this->createFixtureDataForCalculation();
        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $voucherEntity = $this->createDiscounts($discountAmount, DiscountDependencyProvider::PLUGIN_CALCULATOR_FIXED);

        $voucherDiscountTransfer = new DiscountTransfer();
        $voucherDiscountTransfer->setVoucherCode($voucherEntity->getCode());
        $quoteTransfer->addVoucherDiscount($voucherDiscountTransfer);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        //item totals
        $itemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(19, $itemTransfer->getTaxRate());
        $this->assertSame(236, $itemTransfer->getUnitTaxAmount());
        $this->assertSame(473, $itemTransfer->getSumTaxAmount());
        $this->assertSame(236, $itemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(473, $itemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(1500, $itemTransfer->getUnitGrossPrice());
        $this->assertSame(3000, $itemTransfer->getSumGrossPrice());

        $this->assertSame(1500, $itemTransfer->getUnitPrice());
        $this->assertSame(3000, $itemTransfer->getSumPrice());

        $this->assertSame(1500, $itemTransfer->getUnitSubtotalAggregation());
        $this->assertSame(3000, $itemTransfer->getSumSubtotalAggregation());

        $this->assertSame(1480, $itemTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(2960, $itemTransfer->getSumPriceToPayAggregation());

        $this->assertSame(20, $itemTransfer->getUnitDiscountAmountAggregation());
        $this->assertSame(40, $itemTransfer->getSumDiscountAmountAggregation());

        $this->assertSame(20, $itemTransfer->getUnitDiscountAmountFullAggregation());
        $this->assertSame(40, $itemTransfer->getSumDiscountAmountFullAggregation());

        //expenses
        $expenseTransfer = $quoteTransfer->getExpenses()[0];

        $this->assertSame(55, $expenseTransfer->getUnitTaxAmount());
        $this->assertSame(109, $expenseTransfer->getSumTaxAmount());

        $this->assertSame(342, $expenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(684, $expenseTransfer->getSumPriceToPayAggregation());

        $deliveryCostExpenseTransfer = $recalculatedQuoteTransfer->getExpenses()[1];

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitGrossPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumGrossPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $deliveryCostExpenseTransfer->getTaxRate());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getSumTaxAmount());

        //order totals
        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();

        $this->assertSame(3000, $totalsTransfer->getSubtotal());
        $this->assertSame(40, $totalsTransfer->getDiscountTotal());
        $this->assertSame(984, $totalsTransfer->getExpenseTotal());
        $this->assertSame(3944, $totalsTransfer->getGrandTotal());
        $this->assertSame(630, $totalsTransfer->getTaxTotal()->getAmount());
        $this->assertSame(3260, $totalsTransfer->getDisplayTotal());
        $this->assertSame(684, $totalsTransfer->getDepositTotal());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 2x Reissdorf Kölsch          14.00       28.00       16.66       33.32
     *      MwSt (19%)               2.66        5.32
     *
     * 2x Pfand 24x 0.33l            2.87        5.74        3.42        6.84
     *      MwSt (19%)               0.55        1.10
     *
     *      Liefergebür              1.00        1.00        1.19        1.19
     *      MwSt (19%)               0.19        0.19
     *______________________________________________________________________________
     *
     *      Net Total                                                   34.74
     *      Display Total                                               35.60
     *      Deposit Total                                                5.74
     *      zzgl. MwSt                                                   6.60
     *      Gross Total                                                 41.34
     *
     * @return void
     */
    public function testCalculatorStackWithNetTaxMode()
    {
        $calculationFacade = $this->createCalculationFacade();
        $quoteTransfer = $this->createFixtureDataForCalculation();

        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_NET);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        $itemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(19, $itemTransfer->getTaxRate());
        $this->assertSame(266, $itemTransfer->getUnitTaxAmount());
        $this->assertSame(532, $itemTransfer->getSumTaxAmount());
        $this->assertSame(266, $itemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(532, $itemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(1400, $itemTransfer->getUnitNetPrice());
        $this->assertSame(2800, $itemTransfer->getSumNetPrice());

        $this->assertSame(1400, $itemTransfer->getUnitPrice());
        $this->assertSame(2800, $itemTransfer->getSumPrice());

        $this->assertSame(1400, $itemTransfer->getUnitSubtotalAggregation());
        $this->assertSame(2800, $itemTransfer->getSumSubtotalAggregation());

        $this->assertSame(1666, $itemTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(3332, $itemTransfer->getSumPriceToPayAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountFullAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountFullAggregation());

        $expenseTransfer = $recalculatedQuoteTransfer->getExpenses()[0];

        $this->assertSame(287, $expenseTransfer->getUnitNetPrice());
        $this->assertSame(574, $expenseTransfer->getSumNetPrice());

        $this->assertSame(287, $expenseTransfer->getUnitPrice());
        $this->assertSame(574, $expenseTransfer->getSumPrice());

        $this->assertSame(342, $expenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(683, $expenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $expenseTransfer->getTaxRate());
        $this->assertSame(55, $expenseTransfer->getUnitTaxAmount());
        $this->assertSame(109, $expenseTransfer->getSumTaxAmount());

        $deliveryCostExpenseTransfer = $recalculatedQuoteTransfer->getExpenses()[1];

        $this->assertSame(100, $deliveryCostExpenseTransfer->getUnitNetPrice());
        $this->assertSame(100, $deliveryCostExpenseTransfer->getSumNetPrice());

        $this->assertSame(100, $deliveryCostExpenseTransfer->getUnitPrice());
        $this->assertSame(100, $deliveryCostExpenseTransfer->getSumPrice());

        $this->assertSame(119, $deliveryCostExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(119, $deliveryCostExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $deliveryCostExpenseTransfer->getTaxRate());
        $this->assertSame(19, $deliveryCostExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(19, $deliveryCostExpenseTransfer->getSumTaxAmount());

        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();
        $this->assertSame(2800, $totalsTransfer->getSubtotal());
        $this->assertSame(0, $totalsTransfer->getDiscountTotal());
        $this->assertSame(674, $totalsTransfer->getExpenseTotal());
        $this->assertSame(4134, $totalsTransfer->getGrandTotal());
        $this->assertSame(3474, $totalsTransfer->getNetTotal());
        $this->assertSame(660, $totalsTransfer->getTaxTotal()->getAmount());
        $this->assertSame(3560, $totalsTransfer->getDisplayTotal());
        $this->assertSame(574, $totalsTransfer->getDepositTotal());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 2x Reissdorf Kölsch          14.00       28.00       16.66       33.32
     *      MwSt (19%)               2.66        5.32
     *
     * 2x Voucher Code Rabatt       -0.20       -0.40       -0.24       -0.48
     *      MwSt (19%)              -0.04       -0.08
     *
     * 2x Pfand 24x 0.33l            2.87        5.74        3.42        6.84
     *      MwSt (19%)               0.55        1.10
     *
     *      Liefergebür              1.00        1.00        1.19        1.19
     *      MwSt (19%)               0.19        0.19
     *
     *______________________________________________________________________________
     *
     *      Net Total                                                   34.34
     *      Display Total                                               35.12
     *      Discount Total                                              -0.40
     *      Deposit Total                                                5.74
     *      zzgl. MwSt                                                   6.52
     *      Gross Total                                                 40.86
     *
     * @return void
     */
    public function testCalculatorStackWithNetTaxModeAfterDiscounts()
    {
        $calculationFacade = $this->createCalculationFacade();
        $quoteTransfer = $this->createFixtureDataForCalculation();

        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_NET);

        $discountAmount = 20;
        $voucherEntity = $this->createDiscounts($discountAmount, DiscountDependencyProvider::PLUGIN_CALCULATOR_FIXED);

        $voucherDiscountTransfer = new DiscountTransfer();
        $voucherDiscountTransfer->setVoucherCode($voucherEntity->getCode());
        $quoteTransfer->addVoucherDiscount($voucherDiscountTransfer);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        $itemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(19, $itemTransfer->getTaxRate());
        $this->assertSame(262, $itemTransfer->getUnitTaxAmount());
        $this->assertSame(524, $itemTransfer->getSumTaxAmount());
        $this->assertSame(262, $itemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(524, $itemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(1400, $itemTransfer->getUnitNetPrice());
        $this->assertSame(2800, $itemTransfer->getSumNetPrice());

        $this->assertSame(1400, $itemTransfer->getUnitPrice());
        $this->assertSame(2800, $itemTransfer->getSumPrice());

        $this->assertSame(1400, $itemTransfer->getUnitSubtotalAggregation());
        $this->assertSame(2800, $itemTransfer->getSumSubtotalAggregation());

        $this->assertSame(1642, $itemTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(3284, $itemTransfer->getSumPriceToPayAggregation());

        $this->assertSame(20, $itemTransfer->getUnitDiscountAmountAggregation());
        $this->assertSame(40, $itemTransfer->getSumDiscountAmountAggregation());

        $this->assertSame(20, $itemTransfer->getUnitDiscountAmountFullAggregation());
        $this->assertSame(40, $itemTransfer->getSumDiscountAmountFullAggregation());

        $expenseTransfer = $recalculatedQuoteTransfer->getExpenses()[0];

        $this->assertSame(287, $expenseTransfer->getUnitNetPrice());
        $this->assertSame(574, $expenseTransfer->getSumNetPrice());

        $this->assertSame(287, $expenseTransfer->getUnitPrice());
        $this->assertSame(574, $expenseTransfer->getSumPrice());

        $this->assertSame(342, $expenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(683, $expenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $expenseTransfer->getTaxRate());
        $this->assertSame(55, $expenseTransfer->getUnitTaxAmount());
        $this->assertSame(109, $expenseTransfer->getSumTaxAmount());

        $deliveryCostExpenseTransfer = $recalculatedQuoteTransfer->getExpenses()[1];

        $this->assertSame(100, $deliveryCostExpenseTransfer->getUnitNetPrice());
        $this->assertSame(100, $deliveryCostExpenseTransfer->getSumNetPrice());

        $this->assertSame(100, $deliveryCostExpenseTransfer->getUnitPrice());
        $this->assertSame(100, $deliveryCostExpenseTransfer->getSumPrice());

        $this->assertSame(119, $deliveryCostExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(119, $deliveryCostExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $deliveryCostExpenseTransfer->getTaxRate());
        $this->assertSame(19, $deliveryCostExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(19, $deliveryCostExpenseTransfer->getSumTaxAmount());

        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();
        $this->assertSame(2800, $totalsTransfer->getSubtotal());
        $this->assertSame(40, $totalsTransfer->getDiscountTotal());
        $this->assertSame(674, $totalsTransfer->getExpenseTotal());
        $this->assertSame(4086, $totalsTransfer->getGrandTotal());
        $this->assertSame(3434, $totalsTransfer->getNetTotal());
        $this->assertSame(652, $totalsTransfer->getTaxTotal()->getAmount());
        $this->assertSame(3512, $totalsTransfer->getDisplayTotal());
        $this->assertSame(574, $totalsTransfer->getDepositTotal());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 1x Reissdorf Kölsch          14.02       14.02       15.00       15.00
     *      MwSt (7%)                0.98        0.98
     *
     * 1x Pfand 24x 0.33l            2.87        2.87        3.42        3.42
     *      MwSt (19%)               0.55        0.55
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt (19%)               0.48        0.48
     *______________________________________________________________________________
     *
     *      Net Total                                                   19.41
     *      Display Total                                               18.00
     *      Deposit Total                                                3.42
     *      inkl. MwSt                                                   2.01
     *      Gross Total                                                 21.42
     *
     * @return void
     */
    public function testTaxCalculationWhenDifferentRatesUsed()
    {
        $calculationFacade = $this->createCalculationFacade();

        $quoteTransfer = $this->createFixtureDataForCalculation();

        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $abstractProductEntity = $this->createAbstractProductWithTaxSet(7);

        $itemTransfer = $quoteTransfer->getItems()[0];
        $itemTransfer->setQuantity(1);
        $itemTransfer->setIdProductAbstract($abstractProductEntity->getIdProductAbstract());

        $depositExpenseTransfer = $quoteTransfer->getExpenses()[0];
        $depositExpenseTransfer->setQuantity(1);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        //order totals
        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();

        $recalculatedItemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(7.0, $recalculatedItemTransfer->getTaxRate());
        $this->assertSame(98, $recalculatedItemTransfer->getUnitTaxAmount());
        $this->assertSame(98, $recalculatedItemTransfer->getSumTaxAmount());
        $this->assertSame(98, $recalculatedItemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(98, $recalculatedItemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(201, $totalsTransfer->getTaxTotal()->getAmount());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 1x Reissdorf Kölsch          14.02       14.02       15.00       15.00
     *      MwSt (7%)                0.98        0.98
     *
     * 1x Voucher Code Rabatt       -0.17       -0.17       -0.20       -0.20
     *      MwSt (19%)              -0.03       -0.03
     *
     * 1x Pfand 24x 0.33l            2.87        2.87        3.42        3.42
     *      MwSt (19%)               0.55        0.55
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt (19%)               0.48        0.48
     *______________________________________________________________________________
     *
     *      Net Total                                                   19.23
     *      Display Total                                               17.80
     *      Discount Total                                              -0.20
     *      Deposit Total                                                3.42
     *      inkl. MwSt                                                   1.99
     *      Gross Total                                                 21.22
     *
     * @return void
     */
    public function testTaxCalculationWhenDifferentRatesAndDiscountUsed()
    {
        $calculationFacade = $this->createCalculationFacade();

        $quoteTransfer = $this->createFixtureDataForCalculation();
        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $abstractProductEntity = $this->createAbstractProductWithTaxSet(7);

        $depositExpenseTransfer = $quoteTransfer->getExpenses()[0];
        $depositExpenseTransfer->setQuantity(1);

        $itemTransfer = $quoteTransfer->getItems()[0];
        $itemTransfer->setIdProductAbstract($abstractProductEntity->getIdProductAbstract());
        $itemTransfer->setQuantity(1);

        $voucherEntity = $this->createDiscounts(20, DiscountDependencyProvider::PLUGIN_CALCULATOR_FIXED);

        $voucherDiscountTransfer = new DiscountTransfer();
        $voucherDiscountTransfer->setVoucherCode($voucherEntity->getCode());
        $quoteTransfer->addVoucherDiscount($voucherDiscountTransfer);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        //order totals
        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();
        $recalculatedItemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(7.0, $recalculatedItemTransfer->getTaxRate());
        $this->assertSame(97, $recalculatedItemTransfer->getUnitTaxAmount());
        $this->assertSame(97, $recalculatedItemTransfer->getSumTaxAmount());
        $this->assertSame(97, $recalculatedItemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(97, $recalculatedItemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(199, $totalsTransfer->getTaxTotal()->getAmount());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 1x Reissdorf Kölsch          15.00       15.00       15.00       15.00
     *      MwSt (0%)                0.00        0.00
     *
     * 1x Pfand 24x 0.33l            2.87        2.87        3.42        3.42
     *      MwSt (19%)               0.55        0.55
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt                     0.48        0.48
     *______________________________________________________________________________
     *
     *      Net Total                                                   19.41
     *      Display Total                                               18.00
     *      Deposit Total                                                3.42
     *      inkl. MwSt                                                   1.03
     *      Gross Total                                                 21.42
     *
     * @return void
     */
    public function testCalculationWhenTaxExemptionIsUsedShouldUseEmptyTax()
    {
        $calculationFacade = $this->createCalculationFacade();

        $quoteTransfer = $this->createFixtureDataForCalculation();
        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $abstractProductEntity = $this->createAbstractProductWithTaxExemption();

        $itemTransfer = $quoteTransfer->getItems()[0];
        $itemTransfer->setIdProductAbstract($abstractProductEntity->getIdProductAbstract());
        $itemTransfer->setQuantity(1);

        $depositExpenseTransfer = $quoteTransfer->getExpenses()[0];
        $depositExpenseTransfer->setQuantity(1);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        //order totals
        $totalsTransfer = $recalculatedQuoteTransfer->getTotals();

        $recalculatedItemTransfer = $recalculatedQuoteTransfer->getItems()[0];
        $this->assertSame(0.0, $recalculatedItemTransfer->getTaxRate());
        $this->assertSame(0, $recalculatedItemTransfer->getUnitTaxAmount());
        $this->assertSame(0, $recalculatedItemTransfer->getSumTaxAmount());

        $this->assertSame(103, $totalsTransfer->getTaxTotal()->getAmount());
    }

    /**
     * Position                     Net Unit    Net Sum     Gross Unit  Gross Sum
     * _____________________________________________________________________________
     * 2x Reissdorf Kölsch          12.61       25.22       15.00       30.00
     *      MwSt (19%)               2.39        4.78
     *
     * 2x Pfand 24x 0.33l            2.87        5.74        3.42        6.84
     *      MwSt (19%)               0.55        1.10
     *
     *      Liefergebür              2.52        2.52        3.00        3.00
     *      MwSt (19%)               0.48        0.48
     *______________________________________________________________________________
     *
     *      Net Total                                                   33.48
     *      Display Total                                               33.00
     *      Deposit Total                                                6.84
     *      inkl. MwSt                                                   6.36
     *      Gross Total                                                 39.84
     *
     *      Missing Min Value Total                                   9970.00
     *      Missing Min Units Total                                     48
     *
     * @return void
     */
    public function testCalculationStackWithMissingMinValueAndMissingUnits()
    {
        $calculationFacade = $this->createCalculationFacade();

        $quoteTransfer = $this->createFixtureDataForCalculation();
        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);
        $quoteTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setMinValue(1000000);
        $quoteTransfer
            ->getConcreteTimeSlots()
            ->offsetGet(0)
            ->setMinUnits(50);

        $recalculatedQuoteTransfer = $calculationFacade->recalculateQuote($quoteTransfer);

        $itemTransfer = $recalculatedQuoteTransfer->getItems()[0];

        $this->assertSame(19, $itemTransfer->getTaxRate());
        $this->assertSame(239, $itemTransfer->getUnitTaxAmount());
        $this->assertSame(479, $itemTransfer->getSumTaxAmount());
        $this->assertSame(239, $itemTransfer->getUnitTaxAmountFullAggregation());
        $this->assertSame(479, $itemTransfer->getSumTaxAmountFullAggregation());

        $this->assertSame(1500, $itemTransfer->getUnitGrossPrice());
        $this->assertSame(3000, $itemTransfer->getSumGrossPrice());

        $this->assertSame(1500, $itemTransfer->getUnitPrice());
        $this->assertSame(3000, $itemTransfer->getSumPrice());

        $this->assertSame(1500, $itemTransfer->getUnitSubtotalAggregation());
        $this->assertSame(3000, $itemTransfer->getSumSubtotalAggregation());

        $this->assertSame(1500, $itemTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(3000, $itemTransfer->getSumPriceToPayAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountAggregation());

        $this->assertSame(0, $itemTransfer->getUnitDiscountAmountFullAggregation());
        $this->assertSame(0, $itemTransfer->getSumDiscountAmountFullAggregation());

        $depositExpenseTransfer = $recalculatedQuoteTransfer
            ->getExpenses()[0];

        $this->assertSame(342, $depositExpenseTransfer->getUnitGrossPrice());
        $this->assertSame(684, $depositExpenseTransfer->getSumGrossPrice());

        $this->assertSame(342, $depositExpenseTransfer->getUnitPrice());
        $this->assertSame(684, $depositExpenseTransfer->getSumPrice());

        $this->assertSame(342, $depositExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(684, $depositExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $depositExpenseTransfer->getTaxRate());
        $this->assertSame(55, $depositExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(109, $depositExpenseTransfer->getSumTaxAmount());

        $deliveryCostExpenseTransfer = $recalculatedQuoteTransfer
            ->getExpenses()[1];

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitGrossPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumGrossPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPrice());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPrice());

        $this->assertSame(300, $deliveryCostExpenseTransfer->getUnitPriceToPayAggregation());
        $this->assertSame(300, $deliveryCostExpenseTransfer->getSumPriceToPayAggregation());

        $this->assertSame(19.0, $deliveryCostExpenseTransfer->getTaxRate());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getUnitTaxAmount());
        $this->assertSame(48, $deliveryCostExpenseTransfer->getSumTaxAmount());

        $totalsTransfer = $recalculatedQuoteTransfer
            ->getTotals();

        $this->assertSame(3000, $totalsTransfer->getSubtotal());
        $this->assertSame(0, $totalsTransfer->getDiscountTotal());
        $this->assertSame(984, $totalsTransfer->getExpenseTotal());
        $this->assertSame(3984, $totalsTransfer->getGrandTotal());
        $this->assertSame(636, $totalsTransfer->getTaxTotal()->getAmount());
        $this->assertSame(3300, $totalsTransfer->getDisplayTotal());
        $this->assertSame(684, $totalsTransfer->getDepositTotal());
        $this->assertSame(997000, $totalsTransfer->getMissingMinAmountTotal());
        $this->assertSame(48, $totalsTransfer->getMissingMinUnitsTotal());
    }

    /**
     * @return void
     */
    public function testExpensesFromQuoteMatchFirstConcreteTimeSlots(): void
    {
        $calculationFacade = $this
            ->createCalculationFacade();

        $quoteTransfer = $this
            ->createFixtureDataForCalculation();

        $recalculatedQuoteTransfer = $calculationFacade
            ->recalculateQuote($quoteTransfer);

        $this
            ->assertEquals(
                $recalculatedQuoteTransfer
                    ->getExpenses(),
                $recalculatedQuoteTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getExpenses()
            );
    }

    /**
     * @return void
     */
    public function testTotalsFromQuoteMatchFirstConcreteTimeSlots(): void
    {
        $calculationFacade = $this
            ->createCalculationFacade();

        $quoteTransfer = $this
            ->createFixtureDataForCalculation();

        $recalculatedQuoteTransfer = $calculationFacade
            ->recalculateQuote($quoteTransfer);

        $this
            ->assertEquals(
                $recalculatedQuoteTransfer
                    ->getTotals(),
                $recalculatedQuoteTransfer
                    ->getConcreteTimeSlots()
                    ->offsetGet(0)
                    ->getTotals()
            );
    }

    /**
     * @return void
     */
    public function testExpensesFromFirstConcreteTimeSlotMatchSecondConcreteTimeSlot(): void
    {
        $calculationFacade = $this
            ->createCalculationFacade();

        $quoteTransfer = $this
            ->createFixtureDataForCalculation();

        $recalculatedQuoteTransfer = $calculationFacade
            ->recalculateQuote($quoteTransfer);

        $this
            ->assertEquals(
                $recalculatedQuoteTransfer->getConcreteTimeSlots()->offsetGet(0)->getExpenses(),
                $recalculatedQuoteTransfer->getConcreteTimeSlots()->offsetGet(1)->getExpenses()
            );
    }

    /**
     * @return void
     */
    public function testTotalsFromFirstConcreteTimeSlotMatchSecondConcreteTimeSlot(): void
    {
        $calculationFacade = $this
            ->createCalculationFacade();

        $quoteTransfer = $this
            ->createFixtureDataForCalculation();

        $recalculatedQuoteTransfer = $calculationFacade
            ->recalculateQuote($quoteTransfer);

        $this
            ->assertEquals(
                $recalculatedQuoteTransfer->getConcreteTimeSlots()->offsetGet(0)->getTotals(),
                $recalculatedQuoteTransfer->getConcreteTimeSlots()->offsetGet(1)->getTotals()
            );
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createFixtureDataForCalculation()
    {
        $depositExpenseTransfer = (new ExpenseTransfer())
            ->setUnitGrossPrice(342)
            ->setUnitNetPrice(287)
            ->setTaxRate(19)
            ->setQuantity(2)
            ->setIsNegative(false)
            ->setType(
                sprintf(
                    '%s-%s',
                    DepositConstants::DEPOSIT_EXPENSE_TYPE,
                    self::ITEM_SKU
                )
            );

        $deliveryCostExpenseTransfer = (new ExpenseTransfer())
            ->setUnitGrossPrice(300)
            ->setUnitNetPrice(100)
            ->setTaxRate(19)
            ->setQuantity(1)
            ->setIsNegative(false)
            ->setType(
                DeliveryAreaConstants::DELIVERY_COST_EXPENSE_TYPE
            );

        $concreteTimeSlotTransfer1 = (new ConcreteTimeSlotTransfer())
            ->setIdBranch(1)
            ->setMinUnits(0)
            ->setMinValue(0);

        $concreteTimeSlotTransfer2 = (new ConcreteTimeSlotTransfer())
            ->setIdBranch(2)
            ->setMinUnits(0)
            ->setMinValue(0);

        $quoteTransfer = (new QuoteTransfer())
            ->setFkConcreteTimeSlot(1)
            ->setFkBranch(1)
            ->setMinUnits(0)
            ->setMinValue(0)
            ->addConcreteTimeSlots($concreteTimeSlotTransfer1)
            ->addConcreteTimeSlots($concreteTimeSlotTransfer2);

        $currencyTransfer = new CurrencyTransfer();
        $currencyTransfer->setCode('EUR');

        $quoteTransfer->setCurrency($currencyTransfer);

        $quoteTransfer->setPriceMode(CalculationPriceMode::PRICE_MODE_GROSS);

        $shippingAddressTransfer = (new AddressTransfer())
            ->setIso2Code('DE')
            ->setEmail('test@test.com')
            ->setFirstName('Theodor')
            ->setLastName('Tester')
            ->setSalutation(SpySalesOrderAddressTableMap::COL_SALUTATION_MR);

        $quoteTransfer->setShippingAddress($shippingAddressTransfer);

        $itemTransfer = (new ItemTransfer())
            ->setSku(self::ITEM_SKU)
            ->setName(self::ITEM_SKU)
            ->setTaxRate(19)
            ->setQuantity(2)
            ->setUnitGrossPrice(1500)
            ->setUnitNetPrice(1400);

        $quoteTransfer->addItem($itemTransfer);

        $quoteTransfer
            ->addExpense($depositExpenseTransfer)
            ->addExpense($deliveryCostExpenseTransfer);

        return $quoteTransfer;
    }

    /**
     * @param int $discountAmount
     * @param string $calculatorType
     *
     * @return \Orm\Zed\Discount\Persistence\SpyDiscountVoucher
     */
    protected function createDiscounts($discountAmount, $calculatorType)
    {
        $discountVoucherPoolEntity = new SpyDiscountVoucherPool();
        $discountVoucherPoolEntity->setName('test-pool');
        $discountVoucherPoolEntity->setIsActive(true);
        $discountVoucherPoolEntity->save();

        $discountVoucherEntity = new SpyDiscountVoucher();
        $discountVoucherEntity->setCode('spryker-test');
        $discountVoucherEntity->setIsActive(true);
        $discountVoucherEntity->setFkDiscountVoucherPool($discountVoucherPoolEntity->getIdDiscountVoucherPool());
        $discountVoucherEntity->save();

        $discountEntity = new SpyDiscount();
        $discountEntity->setAmount($discountAmount);
        $discountEntity->setDisplayName('test1');
        $discountEntity->setIsActive(1);
        $discountEntity->setValidFrom(new DateTime('1985-07-01'));
        $discountEntity->setValidTo(new DateTime('2050-07-01'));
        $discountEntity->setCalculatorPlugin($calculatorType);
        $discountEntity->setCollectorQueryString('sku = "*"');
        $discountEntity->setFkDiscountVoucherPool($discountVoucherPoolEntity->getIdDiscountVoucherPool());
        $discountEntity->save();

        $discountAmountEntity = new SpyDiscountAmount();
        $currencyEntity = $this->getCurrency();
        $discountAmountEntity->setFkCurrency($currencyEntity->getIdCurrency());
        $discountAmountEntity->setNetAmount($discountAmount);
        $discountAmountEntity->setGrossAmount($discountAmount);
        $discountAmountEntity->setFkDiscount($discountEntity->getIdDiscount());
        $discountAmountEntity->save();

        $discountEntity->reload(true);
        $pool = $discountEntity->getVoucherPool();
        $pool->getDiscountVouchers();

        return $discountVoucherEntity;
    }

    /**
     * @return \Orm\Zed\Currency\Persistence\SpyCurrency
     */
    protected function getCurrency()
    {
        return SpyCurrencyQuery::create()->findOneByCode('EUR');
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\CalculationFacade
     */
    protected function createCalculationFacade()
    {
        return new CalculationFacade();
    }

    /**
     * @return void
     */
    protected function resetCurrentDiscounts()
    {
        $discounts = SpyDiscountQuery::create()->find();
        foreach ($discounts as $discountEntity) {
            $discountEntity->setIsActive(false);
            $discountEntity->save();
        }
    }

    /**
     * @param int $taxRate
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstract
     */
    protected function createAbstractProductWithTaxSet($taxRate)
    {
        $countryEntity = SpyCountryQuery::create()->findOneByIso2Code('DE');

        $taxRateEntity = new SpyTaxRate();
        $taxRateEntity->setRate($taxRate);
        $taxRateEntity->setName('test rate');
        $taxRateEntity->setFkCountry($countryEntity->getIdCountry());
        $taxRateEntity->save();

        $taxSetEntity = $this->createTaxSet();

        $this->createTaxSetTax($taxSetEntity, $taxRateEntity);

        $abstractProductEntity = $this->createAbstractProduct($taxSetEntity);

        return $abstractProductEntity;
    }

    /**
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstract
     */
    protected function createAbstractProductWithTaxExemption()
    {
        $taxRateEntity = new SpyTaxRate();
        $taxRateEntity->setRate(0);
        $taxRateEntity->setName(TaxConstants::TAX_EXEMPT_PLACEHOLDER);
        $taxRateEntity->save();

        $taxSetEntity = $this->createTaxSet();

        $this->createTaxSetTax($taxSetEntity, $taxRateEntity);

        $abstractProductEntity = $this->createAbstractProduct($taxSetEntity);

        return $abstractProductEntity;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductAbstract
     */
    protected function createAbstractProduct(SpyTaxSet $taxSetEntity)
    {
        $abstractProductEntity = new SpyProductAbstract();
        $abstractProductEntity->setSku('test-abstract-sku');
        $abstractProductEntity->setAttributes('');
        $abstractProductEntity->setFkTaxSet($taxSetEntity->getIdTaxSet());
        $abstractProductEntity->save();

        return $abstractProductEntity;
    }

    /**
     * @return \Orm\Zed\Tax\Persistence\SpyTaxSet
     */
    protected function createTaxSet()
    {
        $taxSetEntity = new SpyTaxSet();
        $taxSetEntity->setName('name of tax set');
        $taxSetEntity->save();
        return $taxSetEntity;
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpyTaxSet $taxSetEntity
     * @param \Orm\Zed\Tax\Persistence\SpyTaxRate $taxRateEntity
     *
     * @return void
     */
    protected function createTaxSetTax(SpyTaxSet $taxSetEntity, SpyTaxRate $taxRateEntity)
    {
        $taxSetTaxRateEntity = new SpyTaxSetTax();
        $taxSetTaxRateEntity->setFkTaxSet($taxSetEntity->getIdTaxSet());
        $taxSetTaxRateEntity->setFkTaxRate($taxRateEntity->getIdTaxRate());
        $taxSetTaxRateEntity->save();
    }
}
