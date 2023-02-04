<?php

namespace Pyz\Zed\Sentry\Communication;

use Monolog\Logger;
use Pyz\Shared\Config\Environment;
use Pyz\Zed\Sentry\SentryConfig;
use Sentry;
use Sentry\Integration\EnvironmentIntegration;
use Sentry\Integration\FrameContextifierIntegration;
use Sentry\Integration\IntegrationInterface;
use Sentry\Integration\RequestIntegration;
use Sentry\Integration\TransactionIntegration;
use Sentry\Monolog\Handler as SentryMonologHandler;
use Sentry\State\HubAdapter;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method SentryConfig getConfig()
 */
class SentryCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * Creates an instance of the Sentry Monolog handler from the PHP SDK.
     *
     * It is not covered by the documentation, but there is a helpful GitHub
     * issue about how to use it (see the link below).
     *
     * The default integrations are disabled and some of them added back
     * to exclude all error-handling integrations, because Monolog is supposed
     * to do the error handling.
     *
     * @link https://github.com/getsentry/sentry-docs/issues/1104
     * @see Sentry\Integration\IntegrationRegistry::getDefaultIntegrations
     *
     * @return SentryMonologHandler
     */
    public function createSentryMonologHandler(): SentryMonologHandler
    {
        Sentry\init([
            'dsn' => $this->getConfig()->getSentryDsn(),
            'default_integrations' => false,
            'integrations' => $this->getIntegrations(),
            'environment' => Environment::getEnvironment(),
        ]);

        return new SentryMonologHandler(HubAdapter::getInstance(), Logger::ERROR);
    }

    /**
     * @return IntegrationInterface[]
     */
    protected function getIntegrations(): array
    {
        return [
            new EnvironmentIntegration(),
            new FrameContextifierIntegration(),
            new RequestIntegration(),
            new TransactionIntegration(),
        ];
    }
}
