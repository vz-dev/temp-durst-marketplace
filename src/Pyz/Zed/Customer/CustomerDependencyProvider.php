<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Customer;

use Pyz\Shared\Newsletter\NewsletterConstants;
use Pyz\Zed\Customer\Dependency\Facade\CustomerToIntegraBridge;
use Pyz\Zed\Customer\Dependency\Facade\CustomerToSoapRequestBridge;
use Spryker\Zed\Customer\CustomerDependencyProvider as SprykerCustomerDependencyProvider;
use Spryker\Zed\Customer\Dependency\Plugin\CustomerAnonymizerPluginInterface;
use Spryker\Zed\Customer\Dependency\Plugin\CustomerTransferExpanderPluginInterface;
use Spryker\Zed\CustomerGroup\Communication\Plugin\CustomerAnonymizer\RemoveCustomerFromGroupPlugin;
use Spryker\Zed\CustomerUserConnector\Communication\Plugin\CustomerTransferUsernameExpanderPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Newsletter\Communication\Plugin\CustomerAnonymizer\CustomerUnsubscribePlugin;

class CustomerDependencyProvider extends SprykerCustomerDependencyProvider
{
    public const SALES_FACADE = 'sales facade';
    public const NEWSLETTER_FACADE = 'newsletter facade';

    public const FACADE_SOAP_REQUEST = 'FACADE_SOAP_REQUEST';
    public const FACADE_OMS = 'FACADE_OMS';

    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';

    public const SERVICE_SOAP_REQUEST = 'SERVICE_SOAP_REQUEST';

    public const MERCHANT_FACADE = 'MERCHANT_FACADE';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addSalesFacade($container);

        $container[self::NEWSLETTER_FACADE] = function (Container $container) {
            return $container->getLocator()->newsletter()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addServiceSoapRequest($container);
        $container = $this->addSalesQueryContainer($container);
        $container = $this->addSoapRequestFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addOmsFacade($container);

        return $container;
    }

    /**
     * @return CustomerAnonymizerPluginInterface[]
     */
    protected function getCustomerAnonymizerPlugins()
    {
        return [
            new CustomerUnsubscribePlugin([
                NewsletterConstants::EDITORIAL_NEWSLETTER,
            ]),
            new RemoveCustomerFromGroupPlugin(),
        ];
    }

    /**
     * @return CustomerTransferExpanderPluginInterface[]
     */
    protected function getCustomerTransferExpanderPlugins()
    {
        return [
            new CustomerTransferUsernameExpanderPlugin(),
        ];
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addServiceSoapRequest(Container $container): Container
    {
        $container[static::SERVICE_SOAP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->soapRequest()
                ->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
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
     * @param Container $container
     * @return Container
     */
    protected function addSoapRequestFacade(Container $container): Container
    {
        $container[static::FACADE_SOAP_REQUEST] = function (Container $container) {
            return new CustomerToSoapRequestBridge(
                $container
                    ->getLocator()
                    ->soapRequest()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container[self::SALES_FACADE] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[self::MERCHANT_FACADE] = function (Container $container) {
            return $container
                ->getLocator()
                ->merchant()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return $container
                ->getLocator()
                ->oms()
                ->facade();
        };

        return $container;
    }
}
