<?php
/**
 * Durst - project - TimeSlotExportMailTypePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.09.20
 * Time: 15:58
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

/**
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacade getFacade()
 * @method \Pyz\Zed\DeliveryArea\DeliveryAreaConfig getConfig()
 */
class TimeSlotExportMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const NAME = 'time slot export';

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     */
    public function build(MailBuilderInterface $mailBuilder)
    {
        $mailBuilder->addRecipient(
            $mailBuilder->getMailTransfer()->requireEmail()->getEmail(),
            $this->getConfig()->getExportRecipientName()
        );

        $mailBuilder->setSender(
            'mail.sender.email',
            'mail.sender.name'
        );

        $mailBuilder->setTextTemplate('delivery-area/mail/merchant-time-slot-export.text.twig');

        $mailBuilder->setSubject('mail.subject.time-slot-export');
    }
}
