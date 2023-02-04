<?php
/**
 * Durst - project - SoftwareFeatureSaverPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 03.11.18
 * Time: 08:11
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant;
use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SoftwareFeatureSaverPlugin
 * @package Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant
 * @method SoftwarePackageFacadeInterface getFacade()
 */
class SoftwareFeatureSaverPlugin extends AbstractPlugin implements BranchPostSaverPluginInterface
{

    /**
     * Hydrates the entity object with additional data for the given merchant transfer.
     *
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     * @return void
     */
    public function saveBranch(SpyBranch $entity, BranchTransfer $transfer): void
    {
        $this->addSoftwareFeaturesToBranch($entity, $transfer);
    }

    /**
     * @param SpyBranch $entity
     * @param BranchTransfer $transfer
     */
    protected function addSoftwareFeaturesToBranch(SpyBranch $entity, BranchTransfer $transfer)
    {
        foreach ($transfer->getSoftwareFeatureIds() as $softwareFeatureId) {
            $this
                ->getFacade()
                ->addSoftwareFeatureToBranch($softwareFeatureId, $transfer);
        }
    }
}
