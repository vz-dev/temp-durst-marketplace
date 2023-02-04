<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-30
 * Time: 15:35
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class MerchantWholesaleOrderConfirmCustomerTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'merchant wholesale order confirm customer mail';

    /**
     * Specification:
     * - Returns the name of the MailType
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * Specification:
     * - Builds the MailTransfer
     *
     * @api
     *
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
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
     * @return MerchantWholesaleOrderConfirmCustomerTypePlugin
     */
    protected function setSubject(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setSubject('mail.merchant.order.confirm.customer');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return MerchantWholesaleOrderConfirmCustomerTypePlugin
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setHtmlTemplate('oms/mail/merchant-wholesale-order-confirm-customer.html.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return MerchantWholesaleOrderConfirmCustomerTypePlugin
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-wholesale-order-confirm-customer.text.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return MerchantWholesaleOrderConfirmCustomerTypePlugin
     */
    protected function setSender(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setSender('mail.sender.email', 'mail.sender.name');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return MerchantWholesaleOrderConfirmCustomerTypePlugin
     */
    protected function setRecipient(MailBuilderInterface $mailBuilder): self
    {
        $orderTransfer = $mailBuilder
            ->getMailTransfer()
            ->requireOrder()
            ->getOrder();

        $orderTransfer->requireTotals();

        $orderTransfer
            ->getTotals()
            ->requireGrandTotal()
            ->requireDeliveryCostTotal()
            ->requireDepositTotal()
            ->requireGrossSubtotal();

        $mailBuilder
            ->addRecipient(
            $orderTransfer->getEmail(),
            $orderTransfer->getFirstName() . ' ' . $orderTransfer->getLastName()
        );

        return $this;
    }
}