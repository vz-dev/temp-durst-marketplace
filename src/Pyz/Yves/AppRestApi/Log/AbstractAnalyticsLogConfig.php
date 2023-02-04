<?php
/**
 * Durst - project - AbstractAnalyticsLogConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.07.18
 * Time: 11:27
 */

namespace Pyz\Yves\AppRestApi\Log;


use Monolog\Handler\HandlerInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;

abstract class AbstractAnalyticsLogConfig implements LoggerConfigInterface
{
    const CHANNEL_NAME = 'webservice_log_analytics';

    /**
     * @var HandlerInterface[]
     */
    protected $handler;

    /**
     * AbstractAnalyticsLogConfig constructor.
     * @param HandlerInterface[] $handler
     */
    public function __construct(array $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string
     */
    public function getChannelName()
    {
        return static::CHANNEL_NAME;
    }

    /**
     * @return \Monolog\Handler\HandlerInterface[]
     */
    public function getHandlers()
    {
        return $this
            ->handler;
    }

    /**
     * @return callable[]
     */
    public function getProcessors()
    {
        return [];
    }
}