<?php
/**
 * Durst - project - TourToMailBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.11.19
 * Time: 13:06
 */

namespace Pyz\Zed\Tour\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

class TourToMailBridge implements TourToMailBridgeInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * TourToMailBridge constructor.
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     */
    public function __construct(\Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade)
    {
        $this->mailFacade = $mailFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     */
    public function handleMail(MailTransfer $mailTransfer): void
    {
        $this
            ->mailFacade
            ->handleMail($mailTransfer);
    }
}
