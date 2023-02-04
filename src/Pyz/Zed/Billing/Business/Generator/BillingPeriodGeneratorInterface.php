<?php
/**
 * Durst - project - BillingPeriodGeneratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:15
 */

namespace Pyz\Zed\Billing\Business\Generator;


interface BillingPeriodGeneratorInterface
{
    /**
     * @return void
     */
    public function createBillingPeriods();

    /**
     * @param int $idBranch
     *
     * @return void
     */
    public function createBillingPeriodForBranch(int $idBranch): void;
}
