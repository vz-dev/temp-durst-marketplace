<?php
/**
 * Durst - project - PriceImportToMailBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.10.20
 * Time: 17:00
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

class PriceImportToMailBridge implements PriceImportToMailBridgeInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * PriceImportToMailBridge constructor.
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     */
    public function __construct(
        MailFacadeInterface $mailFacade
    )
    {
        $this->mailFacade = $mailFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     * @return void
     */
    public function handleMail(MailTransfer $mailTransfer): void
    {
        $this
            ->mailFacade
            ->handleMail(
                $mailTransfer
            );
    }
}
