<?php
/**
 * Durst - project - LogToMailBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:12
 */

namespace Pyz\Zed\Log\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

interface LogToMailBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(MailTransfer $mailTransfer): void;
}
