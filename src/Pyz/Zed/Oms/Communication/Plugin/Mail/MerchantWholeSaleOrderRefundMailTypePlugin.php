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
class MerchantWholeSaleOrderRefundMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    const MAIL_TYPE = 'merchant wholesale order refund mail';
    const MAIL_SUBJECT_FORMAT = 'Die Bestellung %s in der Tour %s enthält Retouren';

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
            ->setHtmlTemplate($mailBuilder)
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
        $orderTransfer = $mailBuilder
            ->getMailTransfer()
            ->getOrder();

        if($orderTransfer->getBranch()->getUsesGraphmasters() && $orderTransfer->getConcreteTimeSlot() === null)
        {
            $idTour = 'NULL';
        }else{
            $idTour = $orderTransfer->getConcreteTimeSlot()->getFkConcreteTour();
        }

        $subject = sprintf(
            static::MAIL_SUBJECT_FORMAT,
            $orderTransfer->getInvoiceReference(),
            $idTour
        );

        $mailBuilder->setSubject($subject);

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setHtmlTemplate('oms/mail/merchant-order-refund-mail.html.twig');

        return $this;
    }

    /**
     * @param MailBuilderInterface $mailBuilder
     *
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->setTextTemplate('oms/mail/merchant-order-refund-mail.text.twig');

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

        $orderTransfer = $mailBuilder
            ->getMailTransfer()
            ->getOrder();

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
     * @param MailTransfer $mailTransfer
     *
     * @return void
     */
    protected function assertRequirements(
        MailTransfer $mailTransfer
    ) {
        $mailTransfer
            ->requireOrder()
            ->requireBranch()
            ->requireDriver();

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
