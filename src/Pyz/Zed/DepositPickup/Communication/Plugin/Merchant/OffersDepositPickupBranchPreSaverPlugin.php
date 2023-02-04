<?php

namespace Pyz\Zed\DepositPickup\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class OffersDepositPickupBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{
    /**
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $entity->setOffersDepositPickup($transfer->getOffersDepositPickup());
    }
}
