<?php
/**
 * Durst - project - SoftwarePackageSaverPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.08.18
 * Time: 13:45
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantSaverPluginInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class SoftwarePackageSaverPlugin
 * @package Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant
 * @method SoftwarePackageFacadeInterface getFacade()
 */
class SoftwarePackageSaverPlugin extends AbstractPlugin implements MerchantSaverPluginInterface
{

    /**
     * Hydrates the entity object with additional data for the given merchant transfer.
     *
     * @param SpyMerchant $entity
     * @param MerchantTransfer $transfer
     * @return void
     */
    public function saveMerchant(
        SpyMerchant $entity,
        MerchantTransfer $transfer
    ): void
    {
        $this
            ->getFacade()
            ->saveSoftwarePackageInMerchant($entity, $transfer);
    }
}
