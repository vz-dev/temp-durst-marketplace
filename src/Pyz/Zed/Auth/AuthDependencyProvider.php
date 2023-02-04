<?php


namespace Pyz\Zed\Auth;

use Spryker\Zed\Auth\AuthDependencyProvider as SprykerAuthDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AuthDependencyProvider extends SprykerAuthDependencyProvider
{
    public const FACADE_DRIVER = 'FACADE_DRIVER';
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addDriverFacade($container);
        $container = $this->addSequenceNumberFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDriverFacade(Container $container): Container
    {
        $container[static::FACADE_DRIVER] = function (Container $container) {
            return $container
                ->getLocator()
                ->driver()
                ->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSequenceNumberFacade(Container $container): Container
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container
                ->getLocator()
                ->sequenceNumber()
                ->facade();
        };

        return $container;
    }
}