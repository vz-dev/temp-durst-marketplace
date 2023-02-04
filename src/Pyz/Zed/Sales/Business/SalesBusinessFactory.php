<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Sales\Business;

use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Pyz\Zed\Sales\Business\Helper\Base64ToFileHelper;
use Pyz\Zed\Sales\Business\Helper\Base64ToFileHelperInterface;
use Pyz\Zed\Sales\Business\Model\Comment\OrderCommentReader;
use Pyz\Zed\Sales\Business\Model\Comment\OrderCommentReaderInterface;
use Pyz\Zed\Sales\Business\Model\Customer\IntegraCustomer;
use Pyz\Zed\Sales\Business\Model\Customer\IntegraCustomerInterface;
use Pyz\Zed\Sales\Business\Model\CustomerOrderReader;
use Pyz\Zed\Sales\Business\Model\Expense\ExpenseReader;
use Pyz\Zed\Sales\Business\Model\Expense\ExpenseReaderInterface;
use Pyz\Zed\Sales\Business\Model\Item\ItemGtinHydrator;
use Pyz\Zed\Sales\Business\Model\Item\ItemGtinHydratorInterface;
use Pyz\Zed\Sales\Business\Model\Item\ItemReader;
use Pyz\Zed\Sales\Business\Model\Item\ItemReaderInterface;
use Pyz\Zed\Sales\Business\Model\Item\ItemUpdater;
use Pyz\Zed\Sales\Business\Model\Item\ItemUpdaterInterface;
use Pyz\Zed\Sales\Business\Model\Order\OrderHydrator;
use Pyz\Zed\Sales\Business\Model\Order\OrderReader;
use Pyz\Zed\Sales\Business\Model\Order\OrderReaderInterface;
use Pyz\Zed\Sales\Business\Model\Order\OrderSaver;
use Pyz\Zed\Sales\Business\Model\Order\OrderUpdater;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesExpenseDeflater;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesExpenseDeflaterInterface;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesOrderItemDeflater;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesOrderItemDeflaterInterface;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesRefundsDeflater;
use Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters\SalesRefundsDeflatorInterface;
use Pyz\Zed\Sales\Business\Model\State\SalesOrderItemStateReader;
use Pyz\Zed\Sales\Business\Model\State\SalesOrderItemStateReaderInterface;
use Pyz\Zed\Sales\Dependency\Facade\SalesToCustomerInterface;
use Pyz\Zed\Sales\Dependency\Plugin\DeflateOrderPluginInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Pyz\Zed\Sales\SalesConfig;
use Pyz\Zed\Sales\SalesDependencyProvider;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Model\Order\OrderHydratorInterface;
use Spryker\Zed\Sales\Business\Model\Order\OrderSaverInterface;
use Spryker\Zed\Sales\Business\Model\Order\OrderUpdaterInterface;
use Spryker\Zed\Sales\Business\Model\Order\SalesOrderSaverInterface;
use Spryker\Zed\Sales\Business\SalesBusinessFactory as SprykerSalesBusinessFactory;

/**
 * @method SalesConfig getConfig()
 * @method SalesQueryContainerInterface getQueryContainer()
 */
class SalesBusinessFactory extends SprykerSalesBusinessFactory
{
    /**
     * @return CustomerOrderReader
     */
    public function createCustomerOrderReader()
    {
        return new CustomerOrderReader(
            $this->getQueryContainer(),
            $this->createOrderHydrator(),
            $this->getOmsFacade()
        );
    }

    /**
     * @return OrderSaver|OrderSaverInterface
     */
    public function createOrderSaver()
    {
        return new OrderSaver(
            $this->getCountryFacade(),
            $this->getOmsFacade(),
            $this->createReferenceGenerator(),
            $this->getConfig(),
            $this->getLocaleQueryContainer(),
            $this->getStore(),
            $this->getOrderExpanderPreSavePlugins(),
            $this->createSalesOrderSaverPluginExecutor(),
            $this->createOrderItemMapper(),
            $this->getDeliveryAreaFacade()
        );
    }

    /**
     * @return OrderSaver|SalesOrderSaverInterface
     */
    public function createSalesOrderSaver()
    {
        return new OrderSaver(
            $this->getCountryFacade(),
            $this->getOmsFacade(),
            $this->createReferenceGenerator(),
            $this->getConfig(),
            $this->getLocaleQueryContainer(),
            $this->getStore(),
            $this->getOrderExpanderPreSavePlugins(),
            $this->createSalesOrderSaverPluginExecutor(),
            $this->createOrderItemMapper(),
            $this->getDeliveryAreaFacade()
        );
    }

    /**
     * @return OrderHydrator|OrderHydratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createOrderHydrator(): OrderHydratorInterface
    {
        return new OrderHydrator(
            $this->getQueryContainer(),
            $this->getOmsFacade(),
            $this->getHydrateOrderPlugins(),
            $this->getDeflateOrderPlugins()
        );
    }

    /**
     * @return DeliveryAreaFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDeliveryAreaFacade() : DeliveryAreaFacadeInterface
    {
        return $this
            ->getProvidedDependency(SalesDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return ProductQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getProductQueryContainer(): ProductQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(SalesDependencyProvider::QUERY_CONTAINER_PRODUCT);
    }

    /**
     * @return OrderCommentReader|\Spryker\Zed\Sales\Business\Model\Comment\OrderCommentReaderInterface
     */
    public function createOrderCommentReader(): OrderCommentReaderInterface
    {
        return new OrderCommentReader($this->getQueryContainer());
    }

    /**
     * @return ItemGtinHydratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createItemGtinHydrator(): ItemGtinHydratorInterface
    {
        return new ItemGtinHydrator(
            $this->getProductQueryContainer()
        );
    }

    /**
     * @return Base64ToFileHelperInterface
     */
    public function createBase64FileHelper(): Base64ToFileHelperInterface
    {
         return new Base64ToFileHelper(
             $this->getConfig()
         );
    }

    /**
     * @return OrderUpdater|OrderUpdaterInterface
     */
    public function createOrderUpdater()
    {
        return new OrderUpdater(
            $this->getQueryContainer()
        );
    }

    /**
     * @return SalesOrderItemDeflaterInterface
     */
    public function createSalesOrderItemDeflater() : SalesOrderItemDeflaterInterface
    {
        return new SalesOrderItemDeflater();
    }

    /**
     * @return SalesExpenseDeflaterInterface
     */
    public function createSalesExpenseDeflater() : SalesExpenseDeflaterInterface
    {
        return new SalesExpenseDeflater();
    }

    /**
     * @return SalesRefundsDeflatorInterface
     */
    public function createSalesRefundsDeflater() : SalesRefundsDeflatorInterface
    {
        return new SalesRefundsDeflater();
    }

    /**
     * @return ExpenseReaderInterface
     */
    public function createExpenseReader() : ExpenseReaderInterface
    {
        return new ExpenseReader(
            $this->getQueryContainer()
        );
    }

    /**
     * @return ItemReaderInterface
     */
    public function createItemReader(): ItemReaderInterface
    {
        return new ItemReader(
            $this->getQueryContainer()
        );
    }

    /**
     * @return SalesOrderItemStateReaderInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createSalesOrderItemStateReader(): SalesOrderItemStateReaderInterface
    {
        return new SalesOrderItemStateReader(
            $this->getOmsQueryContainer()
        );
    }

    /**
     * @return IntegraCustomerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createIntegraCustomer(): IntegraCustomerInterface
    {
        return new IntegraCustomer(
            $this->getCustomerFacade()
        );
    }

    /**
     * @return ItemUpdater|ItemUpdaterInterface
     */
    public function createItemUpdater()
    {
        return new ItemUpdater(
            $this->getQueryContainer()
        );
    }

    /**
     * @return DeflateOrderPluginInterface[]
     * @throws ContainerKeyNotFoundException
     */
    protected function getDeflateOrderPlugins()
    {
        return $this->getProvidedDependency(SalesDependencyProvider::SALES_ORDER_DEFLATE_PLUGINS);
    }

    /**
     * @return OmsQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getOmsQueryContainer(): OmsQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(SalesDependencyProvider::QUERY_CONTAINER_OMS);
    }

    /**
     * @return SalesToCustomerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getCustomerFacade(): SalesToCustomerInterface
    {
        return $this
            ->getProvidedDependency(
                SalesDependencyProvider::FACADE_CUSTOMER
            );
    }

    /**
     * @return OrderReaderInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createOrderReader(): OrderReaderInterface
    {
        return new OrderReader(
            $this->getQueryContainer(),
            $this->createOrderHydrator()
        );
    }
}
