<?php
/**
 * Durst - project - MailToMailerBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.03.20
 * Time: 14:37
 */

namespace Pyz\Zed\Mail\Dependency\Mailer;


use Generated\Shared\Transfer\MailAttachmentTransfer;
use Spryker\Zed\Mail\Dependency\Mailer\MailToMailerInterface as SprykerMailToMailerInterface;

interface MailToMailerInterface extends SprykerMailToMailerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailAttachmentTransfer $attachmentTransfer
     */
    public function addAttachment(MailAttachmentTransfer $attachmentTransfer): void;
}
