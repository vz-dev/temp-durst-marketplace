<?php

namespace Pyz\Zed\Invoice;

use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToHeidelpayRestBridge;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridge;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToOmsBridge;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridge;
use Pyz\Zed\Invoice\Dependency\QueryContainer\InvoiceToSalesBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Symfony\Component\Filesystem\Filesystem;

class InvoiceDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_PDF = 'FACADE_PDF';
    public const FACADE_HEIDELPAY_REST = 'FACADE_HEIDELPAY_REST';
    public const FACADE_OMS = 'FACADE_OMS';

    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';

    public const FILE_SYSTEM = 'FILE_SYSTEM';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $this->addMerchantFacade($container);
        $this->addSequenceNumberFacade($container);
        $this->addPdfFacade($container);
        $this->addHeidelpayRestFacade($container);
        $this->addOmsFacade($container);
        $this->addFileSystem($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $this->addSalesQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addSequenceNumberFacade(Container $container): void
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return new InvoiceToSequenceNumberBridge($container
                ->getLocator()
                ->sequenceNumber()
                ->facade());
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addMerchantFacade(Container $container): void
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return new InvoiceToMerchantBridge($container
                ->getLocator()
                ->merchant()
                ->facade());
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return void
     */
    protected function addPdfFacade(Container $container): void
    {
        $container[static::FACADE_PDF] = function (Container $container) {
            return $container
                ->getLocator()
                ->pdf()
                ->facade();
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addHeidelpayRestFacade(Container $container): void
    {
        $container[static::FACADE_HEIDELPAY_REST] = function (Container $container) {
            return new InvoiceToHeidelpayRestBridge(
                $container
                    ->getLocator()
                    ->heidelpayRest()
                    ->facade()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return void
     */
    protected function addOmsFacade(Container $container): void
    {
        $container[static::FACADE_OMS] = function (Container $container) {
            return new InvoiceToOmsBridge(
                $container
                    ->getLocator()
                    ->oms()
                    ->facade()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addSalesQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_SALES] = function (Container $container) {
            return new InvoiceToSalesBridge(
                $container
                    ->getLocator()
                    ->sales()
                    ->queryContainer()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addFileSystem(Container $container): void
    {
        $container[static::FILE_SYSTEM] = function () {
            return new Filesystem();
        };
    }
}
