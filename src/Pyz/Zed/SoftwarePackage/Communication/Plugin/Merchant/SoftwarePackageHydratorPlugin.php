<?php
/**
 * Durst - project - SoftwarePackageHydratorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:30
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantHydratorPluginInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SoftwarePackageHydratorPlugin
 * @package Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant
 * @method SoftwarePackageFacadeInterface getFacade()
 */
class SoftwarePackageHydratorPlugin extends AbstractPlugin implements MerchantHydratorPluginInterface
{

    /**
     * Hydrates the transfer object with additional data for the given merchant entity.
     *
     * @param SpyMerchant $entity
     * @param MerchantTransfer $transfer
     * @return void
     */
    public function hydrateMerchant(
        SpyMerchant $entity,
        MerchantTransfer $transfer
    ): void
    {
        $this
            ->getFacade()
            ->hydrateMerchantBySoftwarePackage($entity, $transfer);
    }
}
