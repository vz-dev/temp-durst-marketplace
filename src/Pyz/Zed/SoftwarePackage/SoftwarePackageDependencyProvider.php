<?php
/**
 * Durst - project - SoftwarePackageDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:31
 */

namespace Pyz\Zed\SoftwarePackage;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class SoftwarePackageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const QUERY_CONTAINER_MERCHANT = 'QUERY_CONTAINER_MERCHANT';

    public const FACADE_MERCHANT = 'FACADE_MERCHANT';

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantQueryContainer($container);

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @param Container $container
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = $this->addMerchantFacade($container);

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
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }
}