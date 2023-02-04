<?php
/**
 * Durst - project - MailLoggerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:30
 */

namespace Pyz\Zed\Log\Business\Log;


interface MailLoggerInterface
{
    /**
     * @param string $subject
     * @param string $errorMessage
     * @return void
     */
    public function mailError(
        string $subject,
        string $errorMessage)
    : void;
}
