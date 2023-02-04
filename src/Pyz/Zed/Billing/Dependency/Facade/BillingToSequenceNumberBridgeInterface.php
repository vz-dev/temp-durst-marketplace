<?php
/**
 * Durst - project - BillingToSequenceNumberBridgeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 13:47
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;

interface BillingToSequenceNumberBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer
     *
     * @return string
     */
    public function generate(SequenceNumberSettingsTransfer $sequenceNumberSettingsTransfer): string;
}
