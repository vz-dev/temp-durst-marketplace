<?php
/**
 * Durst - project - ProductExportToMailBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 08:52
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

class ProductExportToMailBridge implements ProductExportToMailBridgeInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * ProductExportToMailBridge constructor.
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
