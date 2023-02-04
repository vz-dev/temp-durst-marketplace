<?php
/**
 * Durst - project - OrderOnTimeslotBranchPreSaverPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.10.20
 * Time: 15:16
 */

namespace Pyz\Zed\MerchantManagement\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class OrderOnTimeslotBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $entity
            ->setOrderOnTimeslot(
                $transfer->getOrderOnTimeslot()
            );
    }
}
