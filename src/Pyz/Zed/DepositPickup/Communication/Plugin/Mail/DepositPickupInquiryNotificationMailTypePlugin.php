<?php

namespace Pyz\Zed\DepositPickup\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class DepositPickupInquiryNotificationMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const NAME = 'deposit pickup inquiry notification';

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     */
    public function build(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->addRecipient(
            $mailBuilder->getMailTransfer()->requireEmail()->getEmail(),
            $mailBuilder->getMailTransfer()->requireEmail()->getEmail()
        );

        $mailBuilder->setSender(
            'mail.sender.email',
            'mail.sender.name'
        );

        $mailBuilder->setTextTemplate('deposit-pickup/mail/deposit-pickup-inquiry-notification.text.twig');

        $mailBuilder->setSubject('mail.subject.deposit-pickup-inquiry-notification');
    }
}
