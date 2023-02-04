<?php
/**
 * Durst - project - LoggerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 18.11.20
 * Time: 17:11
 */

namespace Pyz\Zed\Integra\Business\Model\Log;

use Orm\Zed\Integra\Persistence\PyzIntegraLog;

interface LoggerInterface
{
    public const LOG_LEVEL_INFO = 'info';
    public const LOG_LEVEL_WARNING = 'warning';
    public const LOG_LEVEL_ERROR = 'error';

    /**
     * @param int $idBranch
     * @param string $level
     * @param string $message
     *
     * @throws PyzIntegraLog
     *
     * @return void
     */
    public function log(int $idBranch, string $level, string $message): void;
}
