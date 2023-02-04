<?php
/**
 * Durst - merchant_center - BranchHydratorPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 11:43
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\MerchantUser;

use ArrayObject;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Orm\Zed\Merchant\Persistence\DstMerchantUser;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantUserHydratorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchHydratorPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\MerchantUser
 * @method MerchantFacadeInterface getFacade()
 */
class BranchHydratorPlugin extends AbstractPlugin implements MerchantUserHydratorPluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     */
    public function hydrateMerchantUser(
        DstMerchantUser $merchantUser,
        MerchantUserTransfer $merchantUserTransfer
    ): void
    {
        $idMerchant = $merchantUser
            ->getFkMerchant();

        $branches = $this
            ->getFacade()
            ->getBranchesByIdMerchant(
                $idMerchant
            );

        $merchantUserTransfer
            ->setBranches(
                new ArrayObject($branches)
            );
    }
}
