<?php
/**
 * Durst - project - BranchUserHydratorPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:01
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\BranchUserTransfer;
use Orm\Zed\Merchant\Persistence\DstBranchUser;

interface BranchUserHydratorPluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     * @return void
     */
    public function hydrateBranchUser(
        DstBranchUser $branchUser,
        BranchUserTransfer $branchUserTransfer
    ): void;
}
