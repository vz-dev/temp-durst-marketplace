<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Log;

use Pyz\Shared\Config\Environment;
use Pyz\Zed\Log\Communication\Plugin\FilebeatLogListenerPlugin;
use Pyz\Zed\Log\Dependency\Facade\LogToMailBridge;
use Pyz\Zed\Sentry\Communication\Plugin\Handler\SentryMonologHandlerPlugin;
use Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Log\Communication\Plugin\Handler\ExceptionStreamHandlerPlugin;
use Spryker\Zed\Log\Communication\Plugin\Handler\StreamHandlerPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\EnvironmentProcessorPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\GuzzleBodyProcessorPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\PsrLogMessageProcessorPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\RequestProcessorPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\ResponseProcessorPlugin;
use Spryker\Zed\Log\Communication\Plugin\Processor\ServerProcessorPlugin;
use Spryker\Zed\Log\LogDependencyProvider as SprykerLogDependencyProvider;
use Spryker\Zed\Propel\Communication\Plugin\Log\EntityProcessorPlugin;

/**
 * @method LogConfig getConfig()
 */
class LogDependencyProvider extends SprykerLogDependencyProvider
{
    public const FACADE_MAIL = 'FACADE_MAIL';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addMailFacade($container);

        return $container;
    }

    /**
     * @return \Spryker\Zed\Log\Business\Model\LogListener\LogListenerInterface[]
     */
    protected function getLogListeners()
    {
        return [
            new FilebeatLogListenerPlugin(),
        ];
    }

    /**
     * @return LogHandlerPluginInterface[]
     */
    protected function getLogHandlers(): array
    {
        $handlers = [
            new StreamHandlerPlugin(),
            new ExceptionStreamHandlerPlugin(),
            //new QueueHandlerPlugin(),
        ];

        if ($this->isSentryHandlerEnabledForCurrentEnvironment()) {
            $handlers[] = new SentryMonologHandlerPlugin();
        }

        return $handlers;
    }

    /**
     * @return bool
     */
    protected function isSentryHandlerEnabledForCurrentEnvironment(): bool
    {
        return in_array(
            Environment::getEnvironment(),
            $this->getConfig()->getLogSentryHandlerEnabledForEnvironments()
        );
    }

    /**
     * @return \Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface[]
     */
    protected function getLogProcessors()
    {
        return [
            new PsrLogMessageProcessorPlugin(),
            new EntityProcessorPlugin(),
            new EnvironmentProcessorPlugin(),
            new ServerProcessorPlugin(),
            new RequestProcessorPlugin(),
            new ResponseProcessorPlugin(),
            new GuzzleBodyProcessorPlugin(),
        ];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMailFacade(Container $container): Container
    {
        $container[static::FACADE_MAIL] = function (Container $container) {
            return new LogToMailBridge(
                $container
                    ->getLocator()
                    ->mail()
                    ->facade()
            );
        };

        return $container;
    }
}
