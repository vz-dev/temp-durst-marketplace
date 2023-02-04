<?php
/**
 * Durst - project - BranchPostRemoveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 21.11.18
 * Time: 14:14
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPostRemovePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchPostRemoveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Merchant
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class BranchPostRemoveTouchPlugin extends AbstractPlugin implements BranchPostRemovePluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given branch entity.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return void
     */
    public function removeBranch(SpyBranch $entity, BranchTransfer $branchTransfer): void
    {
        $this->getFacade()->touchDeleted(MerchantConstants::RESOURCE_TYPE_BRANCH, $entity->getIdBranch());
    }
}
