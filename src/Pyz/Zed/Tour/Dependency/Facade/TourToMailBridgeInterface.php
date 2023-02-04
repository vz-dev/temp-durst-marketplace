<?php
/**
 * Durst - project - TourToMailBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.11.19
 * Time: 13:06
 */

namespace Pyz\Zed\Tour\Dependency\Facade;


use Generated\Shared\Transfer\MailTransfer;

interface TourToMailBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     */
    public function handleMail(MailTransfer $mailTransfer): void;
}
