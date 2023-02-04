<?php
/**
 * Durst - project - SwiftMailer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.03.20
 * Time: 14:31
 */

namespace Pyz\Zed\Mail\Business\Model\Provider;

use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Mail\Dependency\Mailer\MailToMailerInterface;
use Spryker\Zed\Mail\Business\Model\Provider\SwiftMailer as SprykerSwiftMailer;
use Spryker\Zed\Mail\Business\Model\Renderer\RendererInterface;

class SwiftMailer extends SprykerSwiftMailer
{
    /**
     * @var \Spryker\Zed\Mail\Business\Model\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var \Pyz\Zed\Mail\Dependency\Mailer\MailToMailerInterface
     */
    protected $mailer;

    /**
     * SwiftMailer constructor.
     * @param \Spryker\Zed\Mail\Business\Model\Renderer\RendererInterface $renderer
     * @param \Pyz\Zed\Mail\Dependency\Mailer\MailToMailerInterface $mailer
     */
    public function __construct(
        RendererInterface $renderer,
        MailToMailerInterface $mailer
    )
    {
        $this->renderer = $renderer;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function sendMail(MailTransfer $mailTransfer): void
    {
        $this
            ->addSubject($mailTransfer)
            ->addFrom($mailTransfer)
            ->addTo($mailTransfer)
            ->addContent($mailTransfer)
            ->addAttachments($mailTransfer);

        $this
            ->mailer
            ->send();
    }

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     */
    protected function addAttachments(MailTransfer $mailTransfer): void
    {
        foreach ($mailTransfer->getAttachments() as $attachment) {
            $this
                ->mailer
                ->addAttachment($attachment);
        }
    }
}
