<?php
/**
 * Durst - project - LogFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 16:34
 */

namespace Pyz\Zed\Log\Business;

use Spryker\Zed\Log\Business\LogFacadeInterface as SprykerLogFacadeInterface;

interface LogFacadeInterface extends SprykerLogFacadeInterface
{
    /**
     * Send an email with given subject and given error message
     * to the recipients defined in config file
     *
     * @param string $subject
     * @param string $errorMessage
     * @return void
     */
    public function sendErrorMail(
        string $subject,
        string $errorMessage
    ): void;
}
