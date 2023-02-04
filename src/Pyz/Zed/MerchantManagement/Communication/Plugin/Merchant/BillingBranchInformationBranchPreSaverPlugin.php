<?php
/**
 * Durst - project - BranchInformationBranchPreSaverPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.08.21
 * Time: 15:17
 */

namespace Pyz\Zed\MerchantManagement\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class BillingBranchInformationBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(
        SpyBranch $entity,
        BranchTransfer $transfer
    ): void
    {
        $entity
            ->setBillingBranchInformation(
                $transfer
                    ->getBillingBranchInformation()
            );
    }
}
