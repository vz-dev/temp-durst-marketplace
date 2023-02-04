<?php
/**
 * Durst - project - BranchPostSaveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 07.11.18
 * Time: 14:27
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchPostSaveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class BranchPostSaveTouchPlugin extends AbstractPlugin implements BranchPostSaverPluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     *
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $this
            ->getFacade()
            ->touchActive(MerchantConstants::RESOURCE_TYPE_BRANCH, $entity->getIdBranch());
    }
}
