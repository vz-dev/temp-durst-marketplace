<?php
/**
 * Durst - project - MerchantOrderConfirmCutomerSepaTypePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-11-06
 * Time: 15:48
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class MerchantWholesaleOrderConfirmSepaCustomerTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    const MAIL_TYPE = 'merchant order confirm sepa customer mail';

    /**
     * @return string
     */
    public function getName()
    {
        return static::MAIL_TYPE;
    }

    /**
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
     * @return $this
     */
    protected function setSubject(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setSubject('mail.merchant.order.confirm.customer');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setHtmlTemplate('oms/mail/merchant-wholesale-order-confirm-sepa-customer.html.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-wholesale-order-confirm-sepa-customer.text.twig');

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
        $orderTransfer = $mailBuilder->getMailTransfer()->requireOrder()->getOrder();
        $orderTransfer->requireTotals();
        $orderTransfer->getTotals()
            ->requireGrandTotal()
            ->requireDeliveryCostTotal()
            ->requireDepositTotal()
            ->requireGrossSubtotal();

        $mailBuilder->addRecipient(
            $orderTransfer->getEmail(),
            $orderTransfer->getFirstName() . ' ' . $orderTransfer->getLastName()
        );

        return $this;
    }
}
