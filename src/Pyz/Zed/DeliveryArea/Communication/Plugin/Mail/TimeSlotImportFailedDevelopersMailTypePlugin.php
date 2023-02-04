<?php
/**
 * Durst - project - TimeSlotImportFailedDevelopersMailTypePlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-18
 * Time: 20:56
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

/**
 * Class TimeSlotImportFailedDevelopersMailTypePlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Mail
 */
class TimeSlotImportFailedDevelopersMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const NAME = 'developers time slot import failed';

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

        $mailBuilder->setTextTemplate('delivery-area/mail/developers-time-slot-import-failed.text.twig');

        $mailBuilder->setSubject('mail.subject.time-slot-import-failed');
    }
}
