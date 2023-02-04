<?php
/**
 * Durst - project - CurrentBillingReferenceHydratorPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 20:38
 */

namespace Pyz\Zed\Billing\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Merchant\Communication\Plugin\BranchHydratorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class CurrentBillingReferenceHydratorPlugin
 * @package Pyz\Zed\Billing\Communication\Plugin\Merchant
 * @method BillingFacadeInterface getFacade()
 */
class CurrentBillingReferenceHydratorPlugin extends AbstractPlugin implements BranchHydratorPluginInterface
{

    /**
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     * @return void
     */
    public function hydrateBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        if($transfer->getBillingStartDate() !== null)
        {
            $currentBillingReference = $this
                ->getFacade()
                ->getCurrentBillingPeriodForBranchById($transfer->getIdBranch());

            if($currentBillingReference !== null){
                $transfer->setCurrentBillingReference($currentBillingReference->getBillingReference());
            }
        }
    }
}
