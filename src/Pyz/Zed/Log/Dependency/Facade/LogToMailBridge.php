<?php
/**
 * Durst - project - LogToMailBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:12
 */

namespace Pyz\Zed\Log\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

class LogToMailBridge implements LogToMailBridgeInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * LogToMailBridge constructor.
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     */
    public function __construct(
        MailFacadeInterface $mailFacade
    )
    {
        $this->mailFacade = $mailFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(MailTransfer $mailTransfer): void
    {
        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }
}
