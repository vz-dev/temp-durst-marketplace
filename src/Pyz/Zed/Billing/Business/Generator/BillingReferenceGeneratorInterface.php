<?php
/**
 * Durst - project - BillingReferenceGeneratorInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:49
 */

namespace Pyz\Zed\Billing\Business\Generator;


interface BillingReferenceGeneratorInterface
{
    /**
     * @param int $idBranch
     * @return string
     */
    public function createBillingReferenceFromBranchId(int $idBranch): string;
}
