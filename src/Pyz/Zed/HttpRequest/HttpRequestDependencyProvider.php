<?php
/**
 * Durst - project - HttpRequestDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:58
 */

namespace Pyz\Zed\HttpRequest;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class HttpRequestDependencyProvider extends AbstractBundleDependencyProvider
{
    public const SERVICE_HTTP_REQUEST = 'SERVICE_HTTP_REQUEST';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addHttpRequestService($container);

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
}
