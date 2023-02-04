<?php

namespace Pyz\Zed\Billing;

use Pyz\Zed\Billing\Dependency\Facade\BillingToInvoiceBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMoneyBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToPdfBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridge;
use Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Symfony\Component\Filesystem\Filesystem;

class BillingDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_PDF = 'FACADE_PDF';
    public const FACADE_INVOICE = 'FACADE_INVOICE';
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';

    public const QUERY_MERCHANT = 'QUERY_MERCHANT';

    public const FILE_SYSTEM = 'FILE_SYSTEM';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $this->addMerchantFacade($container);
        $this->addSequenceNumberFacade($container);
        $this->addSalesFacade($container);
        $this->addMerchantQueryContainer($container);
        $this->addPdfFacade($container);
        $this->addInvoiceFacade($container);
        $this->addFileSystem($container);
        $this->addMoneyFacade($container);
        $this->addGraphmastersFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     */
    protected function addSequenceNumberFacade(Container $container): void
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return new BillingToSequenceNumberBridge($container
                ->getLocator()
                ->sequenceNumber()
                ->facade());
        };
    }

    /**
     * @param Container $container
     */
    protected function addMerchantFacade(Container $container): void
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return new BillingToMerchantBridge($container
                ->getLocator()
                ->merchant()
                ->facade());
        };
    }

    /**
     * @param Container $container
     */
    protected function addSalesFacade(Container $container): void
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return new BillingToSalesBridge($container
                ->getLocator()
                ->sales()
                ->facade());
        };
    }

    /**
     * @param Container $container
     */
    protected function addPdfFacade(Container $container): void
    {
        $container[static::FACADE_PDF] = function (Container $container) {
            return new BillingToPdfBridge(
                $container
                    ->getLocator()
                    ->pdf()
                    ->facade()
            );
        };
    }

    /**
     * @param Container $container
     */
    protected function addMerchantQueryContainer(Container $container): void
    {
        $container[static::QUERY_MERCHANT] = function (Container $container) {
            return new BillingToMerchantQueryContainerBridge($container
                ->getLocator()
                ->merchant()
                ->queryContainer());
        };
    }

    /**
     * @param Container $container
     */
    protected function addInvoiceFacade(Container $container): void
    {
        $container[static::FACADE_INVOICE] = function (Container $container) {
            return new BillingToInvoiceBridge(
                $container
                    ->getLocator()
                    ->invoice()
                    ->facade()
            );
        };
    }

    /**
     * @param Container $container
     */
    protected function addFileSystem(Container $container): void
    {
        $container[static::FILE_SYSTEM] = function () {
            return new Filesystem();
        };
    }

    /**
     * @param Container $container
     */
    protected function addMoneyFacade(Container $container): void
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new BillingToMoneyBridge(
                $container
                    ->getLocator()
                    ->money()
                    ->facade()
            );
        };
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphmastersFacade(Container $container): Container
    {
        $container[static::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container
                ->getLocator()
                ->graphMasters()
                ->facade();
        };

        return $container;
    }
}
