<?php
/**
 * Durst - project - SoftwareFeatureHydratorPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 03.11.18
 * Time: 08:53
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchHydratorPluginInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SoftwareFeatureHydratorPlugin
 * @package Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant
 * @method SoftwarePackageFacadeInterface getFacade()
 */
class SoftwareFeatureHydratorPlugin extends AbstractPlugin implements BranchHydratorPluginInterface
{
    /**
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     * @return void
     */
    public function hydrateBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $softwareFeatureTransfers = $this
            ->getFacade()
            ->getSoftwareFeaturesByIdBranch($transfer->getIdBranch());

        $this->addSoftwareFeaturesToBranchTransfer($softwareFeatureTransfers, $transfer);
    }

    /**
     * @param SoftwareFeatureTransfer[] $softwareFeatureTransfers
     * @param BranchTransfer $branchTransfer
     */
    protected function addSoftwareFeaturesToBranchTransfer(array $softwareFeatureTransfers, BranchTransfer $branchTransfer){
        $softwareFeatureIds = [];
        foreach($softwareFeatureTransfers as $softwareFeatureTransfer){
            $branchTransfer->addSoftwareFeatures($softwareFeatureTransfer);
            $softwareFeatureIds[] = $softwareFeatureTransfer->getIdSoftwareFeature();
        }

        $branchTransfer->setSoftwareFeatureIds($softwareFeatureIds);
    }
}
