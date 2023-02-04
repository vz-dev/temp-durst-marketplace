<?php
/**
 * Durst - project - MerchantHydratorPluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:02
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;

interface MerchantHydratorPluginInterface
{
    /**
     * Hydrates the transfer object with additional data for the given merchant entity.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $entity
     * @param \Generated\Shared\Transfer\MerchantTransfer $transfer
     * @return void
     */
    public function hydrateMerchant(
        SpyMerchant $entity,
        MerchantTransfer $transfer
    ): void;
}
