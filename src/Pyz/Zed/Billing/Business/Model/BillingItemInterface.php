<?php
/**
 * Durst - project - BillingItemInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 22:51
 */

namespace Pyz\Zed\Billing\Business\Model;

use Generated\Shared\Transfer\BillingItemTransfer;

interface BillingItemInterface
{
    /**
     * @param \Generated\Shared\Transfer\BillingItemTransfer $billingItemTransfer
     *
     * @return BillingItemTransfer
     */
    public function createBillingItem(BillingItemTransfer $billingItemTransfer): BillingItemTransfer;
}
