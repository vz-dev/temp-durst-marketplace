<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Discount;

use Pyz\Zed\DeliveryArea\Communication\Plugin\Discount\DeliveryAreaCollectorPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Discount\DeliveryAreaDecisionRulePlugin;
use Pyz\Zed\Discount\Communication\Plugin\Collector\ItemByBranchCollectorPlugin;
use Pyz\Zed\Discount\Communication\Plugin\DecisionRule\BranchDecisionRulePlugin;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridge;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridge;
use Spryker\Zed\CustomerGroupDiscountConnector\Communication\Plugin\DecisionRule\CustomerGroupDecisionRulePlugin;
use Spryker\Zed\Discount\DiscountDependencyProvider as SprykerDiscountDependencyProvider;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionCalculationFormDataExpanderPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionCalculationFormExpanderPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionCollectorStrategyPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionConfigurationExpanderPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionFilterApplicableItemsPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionFilterCollectedItemsPlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionPostCreatePlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionPostUpdatePlugin;
use Spryker\Zed\DiscountPromotion\Communication\Plugin\Discount\DiscountPromotionViewBlockProviderPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Money\Dependency\Facade\MoneyToCurrencyBridge;
use Spryker\Zed\Money\Dependency\Facade\MoneyToStoreBridge;
use Spryker\Zed\ProductDiscountConnector\Communication\Plugin\Collector\ProductAttributeCollectorPlugin;
use Spryker\Zed\ProductDiscountConnector\Communication\Plugin\DecisionRule\ProductAttributeDecisionRulePlugin;
use Spryker\Zed\ProductLabelDiscountConnector\Communication\Plugin\Collector\ProductLabelCollectorPlugin;
use Spryker\Zed\ProductLabelDiscountConnector\Communication\Plugin\DecisionRule\ProductLabelDecisionRulePlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DecisionRule\ShipmentCarrierDecisionRulePlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DecisionRule\ShipmentMethodDecisionRulePlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DecisionRule\ShipmentPriceDecisionRulePlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DiscountCollector\ItemByShipmentCarrierPlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DiscountCollector\ItemByShipmentMethodPlugin;
use Spryker\Zed\ShipmentDiscountConnector\Communication\Plugin\DiscountCollector\ItemByShipmentPricePlugin;

class DiscountDependencyProvider extends SprykerDiscountDependencyProvider
{
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_MONEY_CURRENCY = 'FACADE_MONEY_CURRENCY';
    public const FACADE_STORE = 'FACADE_STORE';
    public const FACADE_TAX = 'FACADE_TAX';
    public const FACADE_CALCULATION = 'FACADE_CALCULATION';
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';

    public const QUERY_CONTAINER_MERCHANT = 'QUERY_CONTAINER_MERCHANT';
    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        parent::provideBusinessLayerDependencies($container);

        $container = $this->addSequenceNumberFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addMoneyCurrencyFacade($container);
        $container = $this->addCalculationFacade($container);
        $container = $this->addTaxFacade($container);
        $container = $this->addSalesQueryContainer($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addDiscountFacade($container);
        $container = $this->addProductQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        parent::provideCommunicationLayerDependencies($container);

        $container = $this->addMoneyCurrencyFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addMerchantQueryContainer($container);
        $container = $this->addTaxFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSequenceNumberFacade(Container $container): Container
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container->getLocator()->sequenceNumber()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyCurrencyFacade(Container $container): Container
    {
        $container[static::FACADE_MONEY_CURRENCY] = function (Container $container) {
            return new MoneyToCurrencyBridge(
                $container
                ->getLocator()
                ->currency()
                ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container[static::FACADE_STORE] = function (Container $container) {
            return new MoneyToStoreBridge(
                $container
                ->getLocator()
                ->store()
                ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_MERCHANT] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_SALES] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTaxFacade(Container $container): Container
    {
        $container[static::FACADE_TAX] = function (Container $container) {
            return new DiscountToTaxBridge(
                $container
                    ->getLocator()
                    ->tax()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DecisionRulePluginInterface[]
     */
    protected function getDecisionRulePlugins()
    {
        return array_merge(parent::getDecisionRulePlugins(), [
            new ShipmentCarrierDecisionRulePlugin(),
            new ShipmentMethodDecisionRulePlugin(),
            new ShipmentPriceDecisionRulePlugin(),
            new CustomerGroupDecisionRulePlugin(),
            new ProductLabelDecisionRulePlugin(),
            new ProductAttributeDecisionRulePlugin(),
            new BranchDecisionRulePlugin(),
            new DeliveryAreaDecisionRulePlugin(),
        ]);
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\CollectorPluginInterface[]
     */
    protected function getCollectorPlugins()
    {
        return array_merge(parent::getCollectorPlugins(), [
            new ProductLabelCollectorPlugin(),
            new ItemByShipmentCarrierPlugin(),
            new ItemByShipmentMethodPlugin(),
            new ItemByShipmentPricePlugin(),
            new ProductAttributeCollectorPlugin(),
            new ItemByBranchCollectorPlugin(),
            new DeliveryAreaCollectorPlugin(),
        ]);
    }

    /**
     * @return array
     */
    protected function getDiscountableItemFilterPlugins()
    {
        return [
            new DiscountPromotionFilterCollectedItemsPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\CollectorStrategyPluginInterface[]
     */
    protected function getCollectorStrategyPlugins()
    {
        return [
            new DiscountPromotionCollectorStrategyPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DiscountPostCreatePluginInterface[]
     */
    protected function getDiscountPostCreatePlugins()
    {
        return [
            new DiscountPromotionPostCreatePlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DiscountPostUpdatePluginInterface[]
     */
    protected function getDiscountPostUpdatePlugins()
    {
        return [
            new DiscountPromotionPostUpdatePlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DiscountConfigurationExpanderPluginInterface[]
     */
    protected function getDiscountConfigurationExpanderPlugins()
    {
        return [
            new DiscountPromotionConfigurationExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\Form\DiscountFormExpanderPluginInterface[]
     */
    protected function getDiscountFormExpanderPlugins()
    {
        return [
            new DiscountPromotionCalculationFormExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\Form\DiscountFormDataProviderExpanderPluginInterface[]
     */
    protected function getDiscountFormDataProviderExpanderPlugins()
    {
        return [
            new DiscountPromotionCalculationFormDataExpanderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DiscountViewBlockProviderPluginInterface[]
     */
    protected function getDiscountViewTemplateProviderPlugins()
    {
        return [
            new DiscountPromotionViewBlockProviderPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Discount\Dependency\Plugin\DiscountViewBlockProviderPluginInterface[]
     */
    protected function getDiscountApplicableFilterPlugins()
    {
        return [
           new DiscountPromotionFilterApplicableItemsPlugin(),
        ];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCalculationFacade(Container $container): Container
    {
        $container[static::FACADE_CALCULATION] = function (Container $container) {
            return new DiscountToCalculationBridge(
                $container
                    ->getLocator()
                    ->calculation()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDiscountFacade(Container $container): Container
    {
        $container[static::FACADE_DISCOUNT] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container
                ->getLocator()
                ->product()
                ->queryContainer();
        };

        return $container;
    }
}
