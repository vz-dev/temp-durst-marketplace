<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 11:33
 */

namespace Pyz\Zed\Discount\Business;

use Pyz\Zed\Discount\Business\Calculator\CalculatorInterface;
use Pyz\Zed\Discount\Business\Calculator\Discount;
use Pyz\Zed\Discount\Business\Calculator\DiscountInterface;
use Pyz\Zed\Discount\Business\Calculator\FilteredCalculator;
use Pyz\Zed\Discount\Business\Calculator\Type\FixedType;
use Pyz\Zed\Discount\Business\Checkout\GlobalVoucherOrderSaver;
use Pyz\Zed\Discount\Business\Collector\BranchCollector;
use Pyz\Zed\Discount\Business\DecisionRule\BranchDecisionRule;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroup;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroupInterface;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGenerator;
use Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Model\DiscountDisplayNameGenerator;
use Pyz\Zed\Discount\Business\Model\DiscountDisplayNameGeneratorInterface;
use Pyz\Zed\Discount\Business\Model\DiscountModel;
use Pyz\Zed\Discount\Business\Model\DiscountModelInterface;
use Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\BranchHydrator;
use Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupDiscountHydratorInterface;
use Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupHydratorInterface;
use Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\DiscountHydrator;
use Pyz\Zed\Discount\Business\Model\VoucherModel;
use Pyz\Zed\Discount\Business\Model\VoucherModelInterface;
use Pyz\Zed\Discount\Business\Persistence\DiscountConfiguratorHydrate;
use Pyz\Zed\Discount\Business\Validator\CartDiscountAmountGroupValidator;
use Pyz\Zed\Discount\Business\Validator\CartDiscountAvailableGroupValidator;
use Pyz\Zed\Discount\Business\Validator\CartDiscountGroupDateValidation;
use Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface;
use Pyz\Zed\Discount\DiscountConfig;
use Pyz\Zed\Discount\DiscountDependencyProvider;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Spryker\Zed\Discount\Business\Calculator\Type\CalculatorTypeInterface;
use Spryker\Zed\Discount\Business\DiscountBusinessFactory as SprykerDiscountBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

/**
 * Class DiscountBusinessFactory
 * @package Pyz\Zed\Discount\Business
 * @method DiscountQueryContainerInterface getQueryContainer()
 * @method DiscountConfig getConfig()
 */
class DiscountBusinessFactory extends SprykerDiscountBusinessFactory
{
    /**
     * @return DiscountModelInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDiscountModel(): DiscountModelInterface
    {
        return new DiscountModel(
            $this->getQueryContainer(),
            $this->getMerchantFacade(),
            $this->getCurrencyFacade(),
            $this->getTaxFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Model\VoucherModelInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createVoucherModel(): VoucherModelInterface
    {
        return new VoucherModel(
            $this->getCalculationFacade(),
            $this->getCurrencyFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Model\CartDiscountGroupInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartDiscountGroupModel(): CartDiscountGroupInterface
    {
        return new CartDiscountGroup(
            $this->createCartDiscountGroupNameGenerator(),
            $this->getQueryContainer(),
            $this->getMerchantFacade(),
            $this->getCartDiscountGroupHydrators(),
            $this->getCartDiscountGroupDiscountHydrators()
        );
    }

    /**
     * @return DiscountInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDiscount(): DiscountInterface
    {
        $discount = new Discount(
            $this->getQueryContainer(),
            $this->createCalculator(),
            $this->createDecisionRuleBuilder(),
            $this->createVoucherValidator(),
            $this->createDiscountEntityMapper(),
            $this->getMerchantFacade()
        );

        $discount
            ->setDiscountApplicableFilterPlugins(
                $this
                    ->getDiscountApplicableFilterPlugins()
            );

        return $discount;
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Calculator\CalculatorInterface
     */
    protected function createCalculator(): CalculatorInterface
    {
        $calculator = new FilteredCalculator(
            $this->createCollectorBuilder(),
            $this->getMessengerFacade(),
            $this->createDistributor(),
            $this->getCalculatorPlugins(),
            $this->createDiscountableItemFilter(),
            $this->getTaxFacade()
        );

        $calculator->setCollectorStrategyResolver(
            $this->createCollectorResolver()
        );

        return $calculator;
    }

    /**
     * @return DiscountDisplayNameGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDiscountDisplayNameGenerator(): DiscountDisplayNameGeneratorInterface
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getDiscountDisplayNameDefaults();

        return new DiscountDisplayNameGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Model\CartDiscountGroupNameGeneratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartDiscountGroupNameGenerator(): CartDiscountGroupNameGeneratorInterface
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getCartDiscountGroupNameDefaults();

        return new CartDiscountGroupNameGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return BranchDecisionRule
     */
    public function createBranchDecisionRule(): BranchDecisionRule
    {
        return new BranchDecisionRule(
            $this
                ->createComparatorOperators()
        );
    }

    /**
     * @return BranchCollector
     */
    public function createBranchCollector(): BranchCollector
    {
        return new BranchCollector(
            $this
                ->createComparatorOperators()
        );
    }

    /**
     * @return CalculatorTypeInterface
     */
    public function createCalculatorFixedType(): CalculatorTypeInterface
    {
        return new FixedType();
    }

    /**
     * @return DiscountConfiguratorHydrate
     */
    public function createDiscountConfiguratorHydrate(): DiscountConfiguratorHydrate
    {
        $discountConfiguratorHydrate = new DiscountConfiguratorHydrate(
            $this->getQueryContainer(),
            $this->createDiscountEntityMapper()
        );

        $discountConfiguratorHydrate
            ->setDiscountConfigurationExpanderPlugins(
                $this
                    ->getConfigurationExpanderPlugins()
            );

        return $discountConfiguratorHydrate;
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Checkout\GlobalVoucherOrderSaver
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createGlobalVoucherOrderSaver(): GlobalVoucherOrderSaver
    {
        return new GlobalVoucherOrderSaver(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartDiscountGroupValidators(): array
    {
        return [
            $this->createCartDiscountGroupAmountValidator(),
            $this->createCartDiscountGroupDateValidator(),
            $this->createCartDiscountGroupAvailableValidator(),
        ];
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface|CartDiscountAmountGroupValidator
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCartDiscountGroupAmountValidator(): CartDiscountGroupValidatorInterface
    {
        return new CartDiscountAmountGroupValidator(
            $this->getProductQueryContainer(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface|CartDiscountGroupDateValidation
     */
    protected function createCartDiscountGroupDateValidator(): CartDiscountGroupValidatorInterface
    {
        return new CartDiscountGroupDateValidation();
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Validator\CartDiscountGroupValidatorInterface|CartDiscountAvailableGroupValidator
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCartDiscountGroupAvailableValidator(): CartDiscountGroupValidatorInterface
    {
        return new CartDiscountAvailableGroupValidator(
            $this->getDiscountFacade()
        );
    }

    /**
     * @return array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupHydratorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCartDiscountGroupHydrators(): array
    {
        return [
            $this->createCartDiscountGroupBranchHydrator(),
        ];
    }

    /**
     * @return array|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupDiscountHydratorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCartDiscountGroupDiscountHydrators(): array
    {
        return [
            $this->createCartDiscountGroupDiscountHydrator(),
        ];
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupHydratorInterface|\Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\BranchHydrator
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCartDiscountGroupBranchHydrator(): CartDiscountGroupHydratorInterface
    {
        return new BranchHydrator(
            $this->getMerchantFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup\CartDiscountGroupDiscountHydratorInterface|DiscountHydrator
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCartDiscountGroupDiscountHydrator(): CartDiscountGroupDiscountHydratorInterface
    {
        return new DiscountHydrator(
            $this->getDiscountFacade()
        );
    }

    /**
     * @return SequenceNumberFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSequenceNumberFacade(): SequenceNumberFacadeInterface
    {
        return $this
            ->getProvidedDependency(DiscountDependencyProvider::FACADE_SEQUENCE_NUMBER);
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(DiscountDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return \Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCalculationFacade(): DiscountToCalculationBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::FACADE_CALCULATION
            );
    }

    /**
     * @return \Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTaxFacade(): DiscountToTaxBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::FACADE_TAX
            );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDiscountFacade(): DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::FACADE_DISCOUNT
            );
    }

    /**
     * @return \Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::QUERY_CONTAINER_SALES
            );
    }

    /**
     * @return \Pyz\Zed\Product\Persistence\ProductQueryContainer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductQueryContainer(): ProductQueryContainer
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::QUERY_CONTAINER_PRODUCT
            );
    }
}
