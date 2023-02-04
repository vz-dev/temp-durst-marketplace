<?php

namespace Pyz\Zed\Edifact\Communication\Plugin\Merchant;

use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class EdiExcludeMissingItemReturnsBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{
    /**
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $entity->setEdiExcludeMissingItemReturns($transfer->getEdiExcludeMissingItemReturns());
    }
}
