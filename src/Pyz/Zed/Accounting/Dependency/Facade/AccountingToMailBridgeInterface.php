<?php
/**
 * Durst - project - AccountingToMailBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.06.20
 * Time: 16:05
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

interface AccountingToMailBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(
        MailTransfer $mailTransfer
    ): void;
}
