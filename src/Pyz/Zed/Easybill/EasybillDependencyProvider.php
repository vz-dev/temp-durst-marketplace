<?php

namespace Pyz\Zed\Easybill;

use Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridge;
use Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class EasybillDependencyProvider extends AbstractBundleDependencyProvider
{
    public const SERVICE_HTTP_REQUEST = 'SERVICE_HTTP_REQUEST';

    public const CLIENT_QUEUE = 'CLIENT_QUEUE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $this->addHttpRequestFacade($container);
        $this->addQueueClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addHttpRequestFacade(Container $container): void
    {
        $container[static::SERVICE_HTTP_REQUEST] = function (Container $container) {
            return new EasybillToHttpRequestBridge($container
                ->getLocator()
                ->httpRequest()
                ->service());
        };
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     */
    protected function addQueueClient(Container $container): void
    {
        $container[static::CLIENT_QUEUE] = function (Container $container) {
            /** @noinspection PhpUndefinedMethodInspection */
            return new EasybillToQueueBridge($container
                ->getLocator()
                ->queue()
                ->client());
        };
    }
}
