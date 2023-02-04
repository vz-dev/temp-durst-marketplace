<?php
/**
 * Durst - project - MerchantUserHydratorPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:03
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\MerchantUserTransfer;
use Orm\Zed\Merchant\Persistence\DstMerchantUser;

interface MerchantUserHydratorPluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\DstMerchantUser $merchantUser
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return void
     */
    public function hydrateMerchantUser(
        DstMerchantUser $merchantUser,
        MerchantUserTransfer $merchantUserTransfer
    ): void;
}
