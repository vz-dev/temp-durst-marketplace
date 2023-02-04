<?php
/**
 * Durst - project - AccountingDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:12
 */

namespace Pyz\Zed\Accounting;


use Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridge;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridge;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridge;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AccountingDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_LOG = 'FACADE_LOG';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_MAIL = 'FACADE_MAIL';

    public const QUERY_CONTAINER_SEQUENCE_NUMBER = 'QUERY_CONTAINER_SEQUENCE_NUMBER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $this->addLogFacade($container);
        $this->addMerchantFacade($container);
        $this->addSalesFacade($container);
        $this->addSequenceNumberFacade($container);
        $this->addSequenceNumberQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $this->addMailFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addLogFacade(Container $container): void
    {
        $container[static::FACADE_LOG] = function (Container $container) {
            return new AccountingToLogBridge(
                $container
                    ->getLocator()
                    ->log()
                    ->facade()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addMerchantFacade(Container $container): void
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return new AccountingToMerchantBridge(
                $container
                    ->getLocator()
                    ->merchant()
                    ->facade()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addSalesFacade(Container $container): void
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return new AccountingToSalesBridge(
                $container
                    ->getLocator()
                    ->sales()
                    ->facade()
            );
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addSequenceNumberFacade(Container $container): void
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container
                ->getLocator()
                ->sequenceNumber()
                ->facade();
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addSequenceNumberQueryContainer(Container $container): void
    {
        $container[static::QUERY_CONTAINER_SEQUENCE_NUMBER] = function (Container $container) {
            return $container
                ->getLocator()
                ->sequenceNumber()
                ->queryContainer();
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return void
     */
    protected function addMailFacade(Container $container): void
    {
        $container[static::FACADE_MAIL] = function (Container $container) {
            return new AccountingToMailBridge(
                $container
                    ->getLocator()
                    ->mail()
                    ->facade()
            );
        };
    }
}
