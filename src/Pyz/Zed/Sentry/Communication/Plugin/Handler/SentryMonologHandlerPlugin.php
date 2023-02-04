<?php

namespace Pyz\Zed\Sentry\Communication\Plugin\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Pyz\Zed\Sentry\Communication\SentryCommunicationFactory;
use Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method SentryCommunicationFactory getFactory()
 */
class SentryMonologHandlerPlugin extends AbstractPlugin implements LogHandlerPluginInterface
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @return HandlerInterface
     */
    protected function getHandler(): HandlerInterface
    {
        if (!$this->handler) {
            $this->handler = $this->getFactory()->createSentryMonologHandler();
        }

        return $this->handler;
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    public function isHandling(array $record): bool
    {
        return $this->getHandler()->isHandling($record);
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    public function handle(array $record): bool
    {
        return $this->getHandler()->handle($record);
    }

    /**
     * @param array $records
     *
     * @return mixed
     */
    public function handleBatch(array $records)
    {
        return $this->getHandler()->handleBatch($records);
    }

    /**
     * @param callable $callback
     *
     * @return HandlerInterface
     */
    public function pushProcessor($callback): HandlerInterface
    {
        return $this->getHandler()->pushProcessor($callback);
    }

    /**
     * @return callable
     */
    public function popProcessor(): callable
    {
        return $this->getHandler()->popProcessor();
    }

    /**
     * @param FormatterInterface $formatter
     *
     * @return HandlerInterface
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        return $this->getHandler()->setFormatter($formatter);
    }

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->getHandler()->getFormatter();
    }
}
