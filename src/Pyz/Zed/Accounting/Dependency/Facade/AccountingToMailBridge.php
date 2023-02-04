<?php
/**
 * Durst - project - AccountingToMailBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.06.20
 * Time: 16:05
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

class AccountingToMailBridge implements AccountingToMailBridgeInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * AccountingToMailBridge constructor.
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
    public function handleMail(
        MailTransfer $mailTransfer
    ): void
    {
        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }
}
