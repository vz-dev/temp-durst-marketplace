<?php
/**
 * Durst - project - MerchantNotifyReturnTypePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.11.19
 * Time: 11:58
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Mail;


use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmCustomerTypePlugin;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class MerchantNotifyReturnTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'merchant notify return type';

    /**
     * Specification:
     * - Returns the name of the MailType
     *
     * @return string
     * @api
     *
     */
    public function getName()
    {
        return self::MAIL_TYPE;
    }

    /**
     * Specification:
     * - Builds the MailTransfer
     *
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return void
     * @api
     *
     */
    public function build(MailBuilderInterface $mailBuilder)
    {
        $this
            ->assertRequirements($mailBuilder->getMailTransfer());

        $mailBuilder
            ->setHtmlTemplate('tour/mail/notify-merchant-return.html.twig')
            ->setTextTemplate('tour/mail/notify-merchant-return.text.twig')
            ->setSender('mail.sender.email', 'mail.sender.name')
            ->setSubject('mail.tour.notify.merchant.return');

        $this
            ->setRecipient($mailBuilder);
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setRecipient(MailBuilderInterface $mailBuilder): self
    {
        $branchTransfer = $mailBuilder
            ->getMailTransfer()
            ->getBranch();

        $mailBuilder->addRecipient(
            $branchTransfer->getDispatcherEmail(),
            $branchTransfer->getDispatcherName()
        );

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     */
    protected function assertRequirements(MailTransfer $mailTransfer): void
    {
        $mailTransfer
            ->requireTour()
            ->requireBranch();
    }
}
