<?php
/**
 * Durst - project - AccountingToLogBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:15
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


interface AccountingToLogBridgeInterface
{
    /**
     * @param string $subject
     * @param string $errorMessage
     * @return void
     */
    public function sendErrorMail(
        string $subject,
        string $errorMessage
    ): void;
}
