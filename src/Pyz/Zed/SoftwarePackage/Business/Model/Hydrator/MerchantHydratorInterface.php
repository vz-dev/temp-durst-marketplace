<?php
/**
 * Durst - project - MerchantHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:17
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Hydrator;


use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;

interface MerchantHydratorInterface
{
    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return mixed
     */
    public function hydrateMerchantBySoftwarePackage(SpyMerchant $merchantEntity, MerchantTransfer $merchantTransfer);
}