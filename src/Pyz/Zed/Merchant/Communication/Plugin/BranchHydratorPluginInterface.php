<?php
/**
 * Durst - project - BranchHydratorPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:56
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;

interface BranchHydratorPluginInterface
{
    /**
     * Hydrates the transfer object with additional data for the given branch entity.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function hydrateBranch(SpyBranch $entity, BranchTransfer $transfer): void;
}
