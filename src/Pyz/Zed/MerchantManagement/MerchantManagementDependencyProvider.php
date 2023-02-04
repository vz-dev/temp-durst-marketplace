<?php


namespace Pyz\Zed\MerchantManagement;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * Class MerchantManagementDependencyProvider
 * @package Pyz\Zed\MerchantManagement
 */
class MerchantManagementDependencyProvider extends AbstractBundleDependencyProvider
{

    public const FACADE_MERCHANT = 'merchant facade';
    public const FACADE_MONEY = 'money facade';
    public const FACADE_DELIVERY_AREA = 'delivery area facade';
    public const FACADE_DEPOSIT = 'deposit facade';
    public const FACADE_CURRENCY = 'currency facade';
    public const FACADE_TERMS_OF_SERVICE = 'FACADE_TERMS_OF_SERVICE';
    public const FACADE_STATE_MACHINE = 'FACADE_STATE_MACHINE';
    public const FACADE_ACL = 'FACADE_ACL';

    public const QUERY_CONTAINER_MERCHANT = 'merchant query container';
    public const QUERY_CONTAINER_TERMS_OF_SERVICE = 'QUERY_CONTAINER_TERMS_OF_SERVICE';
    public const QUERY_CONTAINER_SOFTWARE_PACKAGE = 'QUERY_CONTAINER_SOFTWARE_PACKAGE';
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';
    public const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';
    public const QUERY_CONTAINER_TOUR = 'QUERY_CONTAINER_TOUR';

    public const SERVICE_DATE_FORMATTER = 'service date formatter';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantFacade($container);
        $container = $this->addMerchantQueryContainer($container);
        $container = $this->addDateFormatterService($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addDepositFacade($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addTermsOfServiceQueryContainer($container);
        $container = $this->addTermsOfServiceFacade($container);
        $container = $this->addSoftwarePackageQueryContainer($container);
        $container = $this->addProductQueryContainer($container);
        $container = $this->addDepositQueryContainer($container);
        $container = $this->addTourQueryContainer($container);
        $container = $this->addStateMachineFacade($container);
        $container = $this->addAclFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantFacade(Container $container) : Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addProductQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDateFormatterService(Container $container) : Container
    {
        $container[static::SERVICE_DATE_FORMATTER] = function (Container $container) {
            return $container->getLocator()->utilDateTime()->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMoneyFacade(Container $container) : Container
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return $container->getLocator()->money()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDeliveryAreaFacade(Container $container) : Container
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
    protected function addDepositFacade(Container $container) : Container
    {
        $container[static::FACADE_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addCurrencyFacade(Container $container) : Container
    {
        $container[static::FACADE_CURRENCY] = function (Container $container) {
            return $container->getLocator()->currency()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTermsOfServiceFacade(Container $container) : Container
    {
        $container[static::FACADE_TERMS_OF_SERVICE] = function (Container $container) {
            return $container->getLocator()->termsOfService()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTermsOfServiceQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_TERMS_OF_SERVICE] = function (Container $container) {
            return $container->getLocator()->termsOfService()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSoftwarePackageQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_SOFTWARE_PACKAGE] = function (Container $container) {
            return $container->getLocator()->softwarePackage()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDepositQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTourQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_TOUR] = function (Container $container) {
            return $container->getLocator()->tour()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addStateMachineFacade(Container $container) : Container
    {
        $container[static::FACADE_STATE_MACHINE] = function (Container $container) {
            return $container->getLocator()->stateMachine()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAclFacade(Container $container): Container
    {
        $container[static::FACADE_ACL] = function (Container $container) {
            return $container
                ->getLocator()
                ->acl()
                ->facade();
        };

        return $container;
    }
}
