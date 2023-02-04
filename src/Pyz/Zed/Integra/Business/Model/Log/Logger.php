<?php
/**
 * Durst - project - Logger.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 18.11.20
 * Time: 17:11
 */

namespace Pyz\Zed\Integra\Business\Model\Log;

use Orm\Zed\Integra\Persistence\Map\PyzIntegraLogTableMap;
use Orm\Zed\Integra\Persistence\PyzIntegraLog;
use Pyz\Zed\Integra\Business\Exception\InvalidValueException;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var \Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Integra\IntegraConfig
     */
    protected $config;

    /**
     * Logger constructor.
     *
     * @param \Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Integra\IntegraConfig $config
     */
    public function __construct(
        IntegraQueryContainerInterface $queryContainer,
        IntegraConfig $config
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }

    /**
     * @param int $idBranch
     * @param string $level
     * @param string $message
     *
     * @throws \Orm\Zed\Integra\Persistence\PyzIntegraLog
     *
     * @return void
     */
    public function log(int $idBranch, string $level, string $message): void
    {
        if ($level !== PyzIntegraLogTableMap::COL_LEVEL_INFO &&
            $level !== PyzIntegraLogTableMap::COL_LEVEL_WARNING &&
            $level !== PyzIntegraLogTableMap::COL_LEVEL_ERROR) {
            throw InvalidValueException::level($level);
        }

        if ($this->checkLevel($level) !== true) {
            return;
        }

        (new PyzIntegraLog())
            ->setFkBranch($idBranch)
            ->setLevel($level)
            ->setMessage($this->cutMessage($message))
            ->save();
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function cutMessage(string $message): string
    {
        if (strlen($message) > 500) {
            return substr($message, 0, 500) . '...';
        }

        return $message;
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    protected function checkLevel(string $level): bool
    {
        switch ($this->config->getLogLevel()) {
            case LoggerInterface::LOG_LEVEL_INFO:
                return true;
            case LoggerInterface::LOG_LEVEL_WARNING:
                return ($level !== LoggerInterface::LOG_LEVEL_ERROR);
            case LoggerInterface::LOG_LEVEL_ERROR:
                return ($level === LoggerInterface::LOG_LEVEL_ERROR);
        }
    }
}
