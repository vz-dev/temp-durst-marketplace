<?php
/**
 * Durst - merchant_center - MerchantOrderInvoiceMailTypePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.08.18
 * Time: 17:30
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;

use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class MerchantOrderInvoiceMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    const MAIL_TYPE = 'merchant order invoice mail';

    /**
     * @return string
     */
    public function getName()
    {
        return static::MAIL_TYPE;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return void
     */
    public function build(MailBuilderInterface $mailBuilder)
    {
        $this
            ->setSubject($mailBuilder)
            ->setHtmlTemplate($mailBuilder)
            ->setTextTemplate($mailBuilder)
            ->setSender($mailBuilder)
            ->setRecipient($mailBuilder);
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setSubject(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setSubject('mail.merchant.order.invoice');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setHtmlTemplate('oms/mail/merchant-order-invoice-mail.html.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-order-invoice-mail.text.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setSender(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setSender('mail.sender.email', 'mail.sender.name');

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setRecipient(MailBuilderInterface $mailBuilder)
    {
        $this->assertRequirements($mailBuilder->getMailTransfer());

        $orderTransfer = $mailBuilder
            ->getMailTransfer()
            ->getOrder();

        $mailBuilder->addRecipient(
            $orderTransfer->getEmail(),
            $orderTransfer->getFirstName() . ' ' . $orderTransfer->getLastName()
        );

        return $this;
    }

    /**
     * @param MailTransfer $mailTransfer
     * @return void
     */
    protected function assertRequirements(
        MailTransfer $mailTransfer
    )
    {
        $mailTransfer
            ->requireOrder()
            ->requireBranch()
            ->requireSurveyUrls();

        $mailTransfer
            ->getOrder()
            ->requireEmail()
            ->requireFirstName()
            ->requireLastName()
            ->requireInvoiceCreatedAt()
            ->requireInvoiceReference()
            ->requirePaymentMethodName();

        $mailTransfer
            ->getBranch()
            ->requireTermsOfService();
    }
}
