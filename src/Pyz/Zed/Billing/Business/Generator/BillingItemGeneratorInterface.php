<?php
/**
 * Durst - project - BillingItemGeneratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-22
 * Time: 06:27
 */

namespace Pyz\Zed\Billing\Business\Generator;

interface BillingItemGeneratorInterface
{
    /**
     * @return void
     */
    public function createBillingItemsForEndedBillingPeriods(): void;

    /**
     * @param int $billingPeriodId
     *
     * @return void
     */
    public function createBillingItemsForBillingPeriodByBillingPeriodId(int $billingPeriodId) : void;
}
