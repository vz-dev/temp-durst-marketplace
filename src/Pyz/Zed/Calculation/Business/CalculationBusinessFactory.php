<?php
/**
 * Durst - project - CalculationBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.08.18
 * Time: 16:28
 */

namespace Pyz\Zed\Calculation\Business;

use Pyz\Zed\Calculation\Business\Model\Aggregator\DiscountAmountAggregator;
use Pyz\Zed\Calculation\Business\Model\Calculator\DiscountTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\ExpenseTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\GrandTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice\PriceGrossCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice\SumGrossPriceCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\InitialGrandTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\NetPrice\PriceNetCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\NetPrice\SumNetPriceCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\NetTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\OrderTaxRateTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\OrderTaxTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\PriceToPayAggregator;
use Pyz\Zed\Calculation\Business\Model\Calculator\RemoveTotalsCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\SubtotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\TaxRateTotalCalculator;
use Pyz\Zed\Calculation\Business\Model\Calculator\TaxTotalCalculator;
use Spryker\Zed\Calculation\Business\CalculationBusinessFactory as SprykerCalculationBusinessFactory;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class CalculationBusinessFactory extends SprykerCalculationBusinessFactory
{
    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\ExpenseTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\ExpenseTotalCalculator
     */
    public function createExpenseTotalCalculator()
    {
        return new ExpenseTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\SubtotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\SubtotalCalculator
     */
    public function createSubtotalCalculator()
    {
        return new SubtotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\GrandTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\GrandTotalCalculator
     */
    public function createGrandTotalCalculator()
    {
        return new GrandTotalCalculator(
            $this->getUtilTextService()
        );
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\TaxTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\TaxTotalCalculator
     */
    public function createTaxTotalCalculator()
    {
        return new TaxTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\OrderTaxTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\OrderTaxTotalCalculator
     */
    public function createOrderTaxTotalCalculator()
    {
        return new OrderTaxTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\InitialGrandTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\InitialGrandTotalCalculator
     */
    public function createInitialGrandTotalCalculator()
    {
        return new InitialGrandTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\PriceToPayAggregator|\Spryker\Zed\Calculation\Business\Model\Aggregator\PriceToPayAggregator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface
     */
    public function createPriceToPayAggregator()
    {
        return new PriceToPayAggregator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\DiscountTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\DiscountTotalCalculator
     */
    public function createDiscountTotalCalculator()
    {
        return new DiscountTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\NetTotalCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Spryker\Zed\Calculation\Business\Model\Calculator\NetTotalCalculator
     */
    public function createNetTotalCalculator()
    {
        return new NetTotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Calculator\RemoveTotalsCalculator|\Spryker\Zed\Calculation\Business\Model\Calculator\RemoveTotalsCalculator
     */
    public function createRemoveTotalsCalculator()
    {
        return new RemoveTotalsCalculator();
    }

    /**
     * @return \Pyz\Zed\Calculation\Business\Model\Aggregator\DiscountAmountAggregator|\Spryker\Zed\Calculation\Business\Model\Aggregator\DiscountAmountAggregator|\Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface
     */
    public function createDiscountAmountAggregatorForGenericAmount()
    {
        return new DiscountAmountAggregator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Pyz\Zed\Calculation\Business\Model\Calculator\NetPrice\SumNetPriceCalculator
     */
    public function createSumNetPriceCalculator(): CalculatorInterface
    {
        return new SumNetPriceCalculator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Pyz\Zed\Calculation\Business\Model\Calculator\NetPrice\PriceNetCalculator
     */
    public function createPriceNetCalculator(): CalculatorInterface
    {
        return new PriceNetCalculator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice\SumGrossPriceCalculator
     */
    public function createSumGrossPriceCalculator(): CalculatorInterface
    {
        return new SumGrossPriceCalculator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface|\Pyz\Zed\Calculation\Business\Model\Calculator\GrossPrice\PriceGrossCalculator
     */
    public function createPriceGrossCalculator(): CalculatorInterface
    {
        return new PriceGrossCalculator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface
     */
    public function createTaxRateTotalCalculator(): CalculatorInterface
    {
        return new TaxRateTotalCalculator();
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface
     */
    public function createOrderTaxRateTotalCalculator(): CalculatorInterface
    {
        return new OrderTaxRateTotalCalculator();
    }
}
