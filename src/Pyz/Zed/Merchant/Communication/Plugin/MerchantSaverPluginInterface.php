<?php
/**
 * Durst - project - MerchantSaverPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:03
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;

interface MerchantSaverPluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given merchant transfer.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $entity
     * @param \Generated\Shared\Transfer\MerchantTransfer $transfer
     * @return void
     */
    public function saveMerchant(
        SpyMerchant $entity,
        MerchantTransfer $transfer
    ): void;
}
