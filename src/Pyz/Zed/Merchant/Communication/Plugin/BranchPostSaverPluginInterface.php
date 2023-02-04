<?php
/**
 * Durst - project - BranchPostSaverPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:00
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;

interface BranchPostSaverPluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given branch transfer.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(
        SpyBranch $entity,
        BranchTransfer $transfer
    ): void;
}
