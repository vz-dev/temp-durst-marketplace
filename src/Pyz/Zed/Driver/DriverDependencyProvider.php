<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 11:27
 */

namespace Pyz\Zed\Driver;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class DriverDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_TOUR = 'FACADE_TOUR';
    const FACADE_MERCHANT = 'FACADE_MERCHANT';

    /**
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container) : Container
    {
        $container = $this->addTourFacade($container);
        $container = $this->addMerchantFacade($container);


        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTourFacade(Container $container) : Container
    {
        $container[static::FACADE_TOUR] = function (Container $container) {
            return $container->getLocator()->tour()->facade();
        };

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

}
