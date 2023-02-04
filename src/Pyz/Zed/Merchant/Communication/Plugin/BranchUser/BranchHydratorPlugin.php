<?php
/**
 * Durst - merchant_center - BranchHydratorPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 18.02.20
 * Time: 12:59
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\BranchUser;

use Generated\Shared\Transfer\BranchUserTransfer;
use Orm\Zed\Merchant\Persistence\DstBranchUser;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Communication\Plugin\BranchUserHydratorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchHydratorPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\BranchUser
 * @method MerchantFacadeInterface getFacade()
 */
class BranchHydratorPlugin extends AbstractPlugin implements BranchUserHydratorPluginInterface
{

    /**
     * {@inheritDoc}
     *
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @param \Generated\Shared\Transfer\BranchUserTransfer $branchUserTransfer
     */
    public function hydrateBranchUser(
        DstBranchUser $branchUser,
        BranchUserTransfer $branchUserTransfer
    ): void
    {
        $branchTransfer = $this
            ->getFacade()
            ->getBranchById(
                $branchUser->getFkBranch()
            );

        $branchUserTransfer
            ->setBranch(
                $branchTransfer
            );
    }
}
