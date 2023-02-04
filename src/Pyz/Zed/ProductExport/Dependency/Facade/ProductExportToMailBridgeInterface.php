<?php
/**
 * Durst - project - ProductExportToMailBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 08:52
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

interface ProductExportToMailBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(MailTransfer $mailTransfer): void;
}
