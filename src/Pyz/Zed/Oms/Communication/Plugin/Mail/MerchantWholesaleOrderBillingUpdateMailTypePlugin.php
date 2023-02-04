<?php

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;

use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Oms\Business\OmsFacade;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

/**
 * @method OmsFacade getFacade()
 * @method OmsCommunicationFactory getFactory()
 */
class MerchantWholesaleOrderBillingUpdateMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    const MAIL_TYPE = 'merchant wholesale order billing update mail';
    const MAIL_SUBJECT_FORMAT = 'Die Abrechnung %s wurde aktualisiert';

    /**
     * @return string
     */
    public function getName()
    {
        return static::MAIL_TYPE;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return void
     */
    public function build(MailBuilderInterface $mailBuilder)
    {
        $this
            ->setSubject($mailBuilder)
            ->setTextTemplate($mailBuilder)
            ->setSender($mailBuilder)
            ->setRecipient($mailBuilder);
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setSubject(MailBuilderInterface $mailBuilder)
    {
        $billingPeriodTransfer = $mailBuilder
            ->getMailTransfer()
            ->getBillingPeriod();

        $subject = sprintf(
            static::MAIL_SUBJECT_FORMAT,
            $billingPeriodTransfer->getBillingReference()
        );

        $mailBuilder->setSubject($subject);

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-order-billing-update-mail.text.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setSender(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setSender('mail.sender.email', 'mail.sender.name');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setRecipient(MailBuilderInterface $mailBuilder)
    {
        $this->assertRequirements($mailBuilder->getMailTransfer());

        $branchTransfer = $mailBuilder
            ->getMailTransfer()
            ->getBranch();

        $mailBuilder->addRecipient(
            $branchTransfer->getEmail(),
            $branchTransfer->getName()
        );

        return $this;
    }

    /**
     * @param MailTransfer $mailTransfer
     */
    protected function assertRequirements(MailTransfer $mailTransfer): void
    {
        $mailTransfer
            ->requireBranch()
            ->requireBillingPeriod();

        $mailTransfer
            ->getBranch()
            ->requireEmail()
            ->requireName();

        $mailTransfer
            ->getBillingPeriod()
            ->requireBillingReference()
            ->requireStartDate()
            ->requireEndDate();
    }
}
