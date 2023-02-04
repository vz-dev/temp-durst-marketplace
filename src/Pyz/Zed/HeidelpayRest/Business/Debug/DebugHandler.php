<?php
/**
 * Durst - project - DebugHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.01.19
 * Time: 13:56
 */

namespace Pyz\Zed\HeidelpayRest\Business\Debug;

use heidelpayPHP\Interfaces\DebugHandlerInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LoggerTrait;

class DebugHandler implements DebugHandlerInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Shared\Log\Config\LoggerConfigInterface
     */
    protected $loggerConfig;

    /**
     * DebugHandler constructor.
     *
     * @param \Spryker\Shared\Log\Config\LoggerConfigInterface $loggerConfig
     */
    public function __construct(LoggerConfigInterface $loggerConfig)
    {
        $this->loggerConfig = $loggerConfig;
    }

    /**
     * This method will allow custom handling of debug output.
     *
     * @param string $message
     *
     * @return void
     */
    public function log(string $message)
    {
        $this->getLogger($this->loggerConfig)
            ->info(
                'heidelpay-rest-debug',
                [
                    DebugLogFormatter::CONTEXT_MESSAGE => $message,
                ]
            );
    }
}
