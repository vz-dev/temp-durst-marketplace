<?php
/**
 * Durst - project - GoogleApiDependencyProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 12:39
 */

namespace Pyz\Zed\GoogleApi;


use Pyz\Zed\GoogleApi\Dependency\GoogleApiToHttpRequestBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class GoogleApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_HTTP_REQUEST = 'FACADE_HTTP_REQUEST';
    public const FACADE_SALES = 'FACADE_SALES';

    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';

    public const SERVICE_HTTP_REQUEST = 'SERVICE_HTTP_REQUEST';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this
            ->addHttpRequestFacade($container);
        $container = $this
            ->addHttpRequestService($container);
        $container = $this
            ->addSalesFacade($container);
        $container = $this
            ->addSalesQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addHttpRequestFacade(Container $container): Container
    {
        $container[static::FACADE_HTTP_REQUEST] = function (Container $container) {
            return new GoogleApiToHttpRequestBridge(
                $container->getLocator()->httpRequest()->facade()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addHttpRequestService(Container $container): Container
    {
        $container[static::SERVICE_HTTP_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->httpRequest()
                ->service();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSalesFacade(Container $container) : Container
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addSalesQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->queryContainer();
        };

        return $container;
    }
}
