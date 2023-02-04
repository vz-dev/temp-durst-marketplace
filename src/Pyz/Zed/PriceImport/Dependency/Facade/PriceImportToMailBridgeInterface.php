<?php
/**
 * Durst - project - PriceImportToMailBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.10.20
 * Time: 17:00
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

interface PriceImportToMailBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(MailTransfer $mailTransfer): void;
}
