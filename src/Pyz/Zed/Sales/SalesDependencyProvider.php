<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Sales;

use Pyz\Zed\DeliveryArea\Communication\Plugin\Sales\ConcreteTimeSlotOrderHydrationPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\Sales\BranchOrderHydratorPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\Sales\PaymentMethodNameHydratePlugin;
use Pyz\Zed\Product\Communication\Plugins\Sales\ProductNameHydrationPlugin;
use Pyz\Zed\Refund\Communication\Plugin\Sales\RefundHydratorPlugin;
use Pyz\Zed\Refund\Communication\Plugin\Sales\ReturnItemHydratorPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Sales\ItemGtinHydratorPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Sales\SalesExpenseDeflaterPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Sales\SalesOrderItemDeflaterPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Sales\SalesRefundsDeflaterPlugin;
use Pyz\Zed\Sales\Dependency\Facade\SalesToCustomerBridge;
use Pyz\Zed\Sales\Dependency\Facade\SalesToIntegraBridge;
use Pyz\Zed\Sales\Dependency\Facade\SalesToInvoiceBridge;
use Pyz\Zed\Sales\Dependency\Facade\SalesToOmsBridge;
use Pyz\Zed\Tour\Communication\Plugin\Sales\TourIdOrderHydratorPlugin;
use Spryker\Zed\Customer\Communication\Plugin\Sales\CustomerOrderHydratePlugin;
use Spryker\Zed\Discount\Communication\Plugin\Sales\DiscountOrderHydratePlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Payment\Communication\Plugin\Sales\PaymentOrderHydratePlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;
use Spryker\Zed\Sales\SalesDependencyProvider as SprykerSalesDependencyProvider;
use Spryker\Zed\SalesProductConnector\Communication\Plugin\Sales\ItemMetadataHydratorPlugin;
use Spryker\Zed\SalesProductConnector\Communication\Plugin\Sales\ProductIdHydratorPlugin;
use Spryker\Zed\Shipment\Communication\Plugin\ShipmentOrderHydratePlugin;

class SalesDependencyProvider extends SprykerSalesDependencyProvider
{
    public const FACADE_DELIVERY_AREA = 'FACADE_DELIVERY_AREA';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';
    public const FACADE_INVOICE = 'FACADE_INVOICE';

    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';
    public const QUERY_CONTAINER_OMS = 'QUERY_CONTAINER_OMS';

    public const SALES_ORDER_DEFLATE_PLUGINS = 'SALES_ORDER_DEFLATE_PLUGINS';

    /**
     * @return HydrateOrderPluginInterface[]
     */
    protected function getOrderHydrationPlugins()
    {
        return [
            new ProductIdHydratorPlugin(),
            new DiscountOrderHydratePlugin(),
            new ShipmentOrderHydratePlugin(),
            new PaymentOrderHydratePlugin(),
            new CustomerOrderHydratePlugin(),
            new ItemMetadataHydratorPlugin(),
            new ItemGtinHydratorPlugin(),
            new ConcreteTimeSlotOrderHydrationPlugin(),
            new ProductNameHydrationPlugin(),
            new PaymentMethodNameHydratePlugin(),
            new BranchOrderHydratorPlugin(),
            new RefundHydratorPlugin(),
            new TourIdOrderHydratorPlugin(),
            new ReturnItemHydratorPlugin(),
        ];
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addProductQueryContainer($container);
        $container = $this->addOrderDeflaterPlugins($container);
        $container = $this->addOmsQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addInvoiceFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDeliveryAreaFacade(Container $container): Container
    {
        $container[static::FACADE_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
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

    /**
     * @return HydrateOrderPluginInterface[]
     */
    protected function getSalesOrderDeflaterPlugins() : array
    {
        return [
            new SalesOrderItemDeflaterPlugin(),
            new SalesExpenseDeflaterPlugin(),
            new SalesRefundsDeflaterPlugin(),
        ];
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addOrderDeflaterPlugins(Container $container) : Container
    {
        $container[static::SALES_ORDER_DEFLATE_PLUGINS] = function (Container $container) {
            return $this->getSalesOrderDeflaterPlugins();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addOmsFacade(Container $container)
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return new SalesToOmsBridge($container->getLocator()->oms()->facade());
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantFacade(Container $container)
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addOmsQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addIntegraFacade(Container $container): Container
    {
        $container[static::FACADE_INTEGRA] = function (Container $container) {
            return new SalesToIntegraBridge(
                $container
                    ->getLocator()
                    ->integra()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container[static::FACADE_CUSTOMER] = function (Container $container) {
            return new SalesToCustomerBridge(
                $container
                    ->getLocator()
                    ->customer()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addInvoiceFacade(Container $container): Container
    {
        $container[static::FACADE_INVOICE] = function (Container $container) {
            return new SalesToInvoiceBridge(
                $container
                    ->getLocator()
                    ->invoice()
                    ->facade()
            );
        };

        return $container;
    }
}
