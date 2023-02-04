<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class StuckOrdersNotificationMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const NAME = 'stuck orders notification';

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
        $mailBuilder
            ->setSender('mail.sender.email', 'mail.sender.name')
            ->setSubject('mail.subject.stuck-orders-notification')
            ->setHtmlTemplate('Oms/Mail/stuck-orders-notification.html.twig')
            ->setTextTemplate('Oms/Mail/stuck-orders-notification.text.twig');
    }
}
