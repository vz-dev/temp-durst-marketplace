<?php
/**
 * Durst - project - BranchPostRemovePluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:59
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;

interface BranchPostRemovePluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given branch entity.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function removeBranch(
        SpyBranch $entity,
        BranchTransfer $transfer
    ): void;
}
