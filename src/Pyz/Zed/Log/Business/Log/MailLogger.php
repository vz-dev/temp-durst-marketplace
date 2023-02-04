<?php
/**
 * Durst - project - MailLogger.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:31
 */

namespace Pyz\Zed\Log\Business\Log;


use ArrayObject;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Log\Communication\Plugin\Mail\ErrorMailTypePlugin;
use Pyz\Zed\Log\Dependency\Facade\LogToMailBridgeInterface;
use Pyz\Zed\Log\LogConfig;

class MailLogger implements MailLoggerInterface
{
    /**
     * @var \Pyz\Zed\Log\Dependency\Facade\LogToMailBridgeInterface
     */
    protected $mailFacade;

    /**
     * @var \Pyz\Zed\Log\LogConfig
     */
    protected $config;

    /**
     * MailLogger constructor.
     * @param \Pyz\Zed\Log\Dependency\Facade\LogToMailBridgeInterface $mailFacade
     * @param \Pyz\Zed\Log\LogConfig $config
     */
    public function __construct(
        LogToMailBridgeInterface $mailFacade,
        LogConfig $config
    )
    {
        $this->mailFacade = $mailFacade;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $subject
     * @param string $errorMessage
     * @return void
     */
    public function mailError(string $subject, string $errorMessage): void
    {
        $mailTransfer = $this
            ->createMailTransfer()
            ->setSubject($subject)
            ->setMessage($errorMessage)
            ->setType(ErrorMailTypePlugin::MAIL_TYPE)
            ->setRecipients($this->createMailRecipients());

        $this
            ->mailFacade
            ->handleMail(
                $mailTransfer
            );
    }

    /**
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        return new MailTransfer();
    }

    /**
     * @return \ArrayObject
     */
    protected function createMailRecipients(): ArrayObject
    {
        $recipients = new ArrayObject();

        foreach ($this->config->getLogMailRecipients() as $email => $name) {
            $transfer = new MailRecipientTransfer();
            $transfer
                ->setEmail($email)
                ->setName($name);

            $recipients
                ->append($transfer);
        }

        return $recipients;
    }
}
