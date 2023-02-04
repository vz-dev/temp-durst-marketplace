<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 20.10.17
 * Time: 13:24
 */

namespace Pyz\Zed\Deposit\Business;

use Pyz\Zed\Deposit\Business\Calculator\Calculator;
use Pyz\Zed\Deposit\Business\Calculator\DepositTaxRateCalculator;
use Pyz\Zed\Deposit\Business\Calculator\GrossMode\GrossSumDepositCalculator;
use Pyz\Zed\Deposit\Business\Calculator\GrossMode\GrossUnitDepositCalculator;
use Pyz\Zed\Deposit\Business\Calculator\TotalCalculator;
use Pyz\Zed\Deposit\Business\Checkout\DepositOrderSaver;
use Pyz\Zed\Deposit\Business\Checkout\DepositSalesExpenseExpander;
use Pyz\Zed\Deposit\Business\Hydrator\DepositAmountOrderItemHydrator;
use Pyz\Zed\Deposit\Business\Model\Deposit;
use Pyz\Zed\Deposit\Business\Model\DepositManager;
use Pyz\Zed\Deposit\Business\Order\Checker as OrderChecker;
use Pyz\Zed\Deposit\Business\Sales\SalesExpenseDepositDeflator;
use Pyz\Zed\Deposit\Business\Sales\SalesExpenseDepositDeflatorInterface;
use Pyz\Zed\Deposit\DepositDependencyProvider;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;

/**
 * Class DepositBusinessFactory
 * @package Pyz\Zed\Deposit\Business
 * @method DepositQueryContainerInterface getQueryContainer()
 */
class DepositBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Deposit\Business\Model\Deposit
     */
    public function createDepositModel()
    {
        return new Deposit(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Calculator\Calculator
     */
    public function createCalculator(): Calculator
    {
        return new Calculator(
            $this->createGrossCalculators()
        );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Model\DepositManager
     */
    public function createDepositManager(): DepositManager
    {
        return new DepositManager(
            $this->createDepositModel()
        );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Calculator\TotalCalculator
     */
    public function createTotalCalculator(): TotalCalculator
    {
        return new TotalCalculator();
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Calculator\DepositTaxRateCalculator
     */
    public function createDepositTaxRateCalculator(): DepositTaxRateCalculator
    {
        return new DepositTaxRateCalculator(
            $this->getTaxFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Checkout\DepositOrderSaver
     */
    public function createDepositOrderSaver(): DepositOrderSaver
    {
        return new DepositOrderSaver(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Order\Checker
     */
    public function createOrderChecker(): OrderChecker
    {
        return new OrderChecker(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return \Spryker\Zed\Tax\Business\Model\CalculatorInterface[]
     */
    protected function createGrossCalculators(): array
    {
        return [
            $this->createGrossUnitDepositCalculator(),
            $this->createSumDepositCalculator(),
        ];
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Calculator\GrossMode\GrossUnitDepositCalculator
     */
    protected function createGrossUnitDepositCalculator(): GrossUnitDepositCalculator
    {
        return new GrossUnitDepositCalculator();
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Calculator\GrossMode\GrossSumDepositCalculator
     */
    protected function createSumDepositCalculator(): GrossSumDepositCalculator
    {
        return new GrossSumDepositCalculator();
    }

    /**
     * @return \Spryker\Zed\Tax\Business\TaxFacadeInterface
     */
    protected function getTaxFacade(): TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(DepositDependencyProvider::FACADE_TAX);
    }

    /**
     * @return \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(DepositDependencyProvider::QUERY_CONTAINER_SALES);
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\Hydrator\DepositAmountOrderItemHydrator
     */
    public function createDepositAmountOrderItemHydrator(): DepositAmountOrderItemHydrator
    {
        return new DepositAmountOrderItemHydrator(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return DepositSalesExpenseExpander
     */
    public function createDepositSalesExpenseExpander(): DepositSalesExpenseExpander
    {
        return new DepositSalesExpenseExpander();
    }

    /**
     * @return SalesExpenseDepositDeflatorInterface
     */
    public function createSalesExpenseDepositDeflator(): SalesExpenseDepositDeflatorInterface
    {
        return new SalesExpenseDepositDeflator();
    }
}
