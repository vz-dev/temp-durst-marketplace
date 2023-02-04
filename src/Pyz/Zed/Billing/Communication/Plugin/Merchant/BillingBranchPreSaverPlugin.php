<?php
/**
 * Durst - project - BillingBranchPreSaverPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-28
 * Time: 13:14
 */

namespace Pyz\Zed\Billing\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BillingBranchSaverPlugin
 * @package Pyz\Zed\Billing\Communication\Plugin\Merchant
 * @method \Pyz\Zed\Billing\Business\BillingFacadeInterface getFacade()
 * @method \Pyz\Zed\Billing\Communication\BillingCommunicationFactory getFactory()
 */
class BillingBranchPreSaverPlugin extends AbstractPlugin implements BranchPreSaverPluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given branch transfer.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyBranch $entity
     * @param \Generated\Shared\Transfer\BranchTransfer $transfer
     *
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $entity
            ->setBillingStartDate($transfer->getBillingStartDate())
            ->setBillingCycle($transfer->getBillingCycle())
            ->setBillingEndOfMonth($transfer->getBillingEndOfMonth());
    }
}
