<?php
/**
 * Durst - project - MerchantWholeSaleOrderRefundMailTypePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-21
 * Time: 14:38
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

/**
 * @method \Pyz\Zed\Oms\Business\OmsFacade getFacade()
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 */
class MerchantWholesaleOrderInvalidCustomerMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    const MAIL_TYPE = 'merchant wholesale order invalid mail';
    const MAIL_SUBJECT_FORMAT = 'Deine Bestellung %s wurde abgelehnt';

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
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return static
     */
    protected function setSubject(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setSubject(
            sprintf(
                static::MAIL_SUBJECT_FORMAT,
                $orderTransfer = $mailBuilder
                    ->getMailTransfer()
                    ->requireOrder()
                    ->getOrder()
                    ->getOrderReference()
            )
        );

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-wholesale-order-invalid-customer.text.twig');

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setHtmlTemplate('oms/mail/merchant-wholesale-order-invalid-customer.html.twig');

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     *
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
