<?php
/**
 * Durst - project - CancelOrderSepaDirectDebitTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.09.21
 * Time: 14:02
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class CancelOrderSepaDirectDebitTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'cancel order with sepa payment';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return void
     */
    public function build(
        MailBuilderInterface $mailBuilder
    ): void
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
    protected function setSubject(
        MailBuilderInterface $mailBuilder
    ): self
    {
        $mailBuilder->setSubject('mail.subject.cancel-order-customer');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setHtmlTemplate(
        MailBuilderInterface $mailBuilder
    ): self
    {
        $mailBuilder->setHtmlTemplate('cancel-order/mail/cancel-order-sepa-direct-debit.html.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setTextTemplate(
        MailBuilderInterface $mailBuilder
    ): self
    {
        $mailBuilder->setTextTemplate('cancel-order/mail/cancel-order-sepa-direct-debit.text.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setSender(
        MailBuilderInterface $mailBuilder
    ): self
    {
        $mailBuilder->setSender('mail.sender.email', 'mail.sender.name');

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setRecipient(
        MailBuilderInterface $mailBuilder
    ): self
    {
        $orderTransfer = $mailBuilder
            ->getMailTransfer()
            ->requireOrder()
            ->getOrder();

        $mailBuilder->addRecipient(
            $orderTransfer->getEmail(),
            $orderTransfer->getFirstName() . ' ' . $orderTransfer->getLastName()
        );

        return $this;
    }
}
