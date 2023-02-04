<?php
/**
 * Durst - merchant_center - MerchantHydratorPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 11:33
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\MerchantUser;

use Generated\Shared\Transfer\MerchantUserTransfer;
use Orm\Zed\Merchant\Persistence\DstMerchantUser;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantUserHydratorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class MerchantHydratorPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\MerchantUser
 * @method MerchantFacadeInterface getFacade()
 */
class MerchantHydratorPlugin extends AbstractPlugin implements MerchantUserHydratorPluginInterface
{
    /**
     * {@inheritDoc}
     *
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

        $merchantTransfer = $this
            ->getFacade()
            ->getMerchantById(
                $idMerchant
            );

        $merchantUserTransfer
            ->setMerchant(
                $merchantTransfer
            );
    }
}
