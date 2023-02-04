<?php
/**
 * Durst - project - MailToMailerBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.03.20
 * Time: 14:36
 */

namespace Pyz\Zed\Mail\Dependency\Mailer;

use Generated\Shared\Transfer\MailAttachmentTransfer;
use Spryker\Zed\Mail\Dependency\Mailer\MailToMailerBridge as SprykerMailToMailerBridge;
use Swift_Attachment;

class MailToMailerBridge extends SprykerMailToMailerBridge implements MailToMailerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailAttachmentTransfer $attachmentTransfer
     * @return void
     */
    public function addAttachment(MailAttachmentTransfer $attachmentTransfer): void
    {
        $attachment = Swift_Attachment::fromPath($attachmentTransfer->getAttachmentUrl())
            ->setFilename($attachmentTransfer->getFileName());

        $this
            ->message
            ->attach(
                $attachment
            );
    }
}
