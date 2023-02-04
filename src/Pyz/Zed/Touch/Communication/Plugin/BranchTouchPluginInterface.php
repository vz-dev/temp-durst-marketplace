<?php
/**
 * Durst - project - BranchTouchPluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 07.11.18
 * Time: 14:11
 */

namespace Pyz\Zed\Touch\Communication\Plugin;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;

interface BranchTouchPluginInterface
{
    /**
     * Adds a Active Touch Record for the provided Branch
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     *
     * @return void
     */
    public function insertActiveTouch(SpyBranch $entity, BranchTransfer $transfer);

    /**
     * Adds a Delete Touch Record for the provided Branch
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     *
     * @return void
     */
    public function insertDeleteTouch(SpyBranch $entity, BranchTransfer $transfer);
}
