<?php
/**
 * Durst - project - DebugLogConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.01.19
 * Time: 09:55
 */

namespace Pyz\Zed\HeidelpayRest\Business\Debug;

use Spryker\Shared\Log\Config\LoggerConfigInterface;

class DebugLogConfig implements LoggerConfigInterface
{
    public const CHANNEL_NAME = 'heidelpay_rest_debug_log';

    /**
     * @var \Monolog\Handler\HandlerInterface[]
     */
    protected $handler;

    /**
     * AbstractAnalyticsLogConfig constructor.
     *
     * @param \Monolog\Handler\HandlerInterface[] $handler
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
