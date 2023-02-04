<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 12:56
 */

namespace Pyz\Zed\Edifact;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class EdifactDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_TOUR = 'FACADE_TOUR';
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container):Container
    {
        $container = $this->addTourFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addGraphMastersFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addTourFacade(Container $container): Container
    {
        $container[self::FACADE_TOUR] = function (Container $container) {
            return $container->getLocator()->tour()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[self::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphMastersFacade(Container $container): Container
    {
        $container[self::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container->getLocator()->graphMasters()->facade();
        };

        return $container;
    }
}
