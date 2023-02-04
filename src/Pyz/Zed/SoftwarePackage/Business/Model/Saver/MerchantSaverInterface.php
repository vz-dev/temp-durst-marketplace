<?php
/**
 * Durst - project - MerchantSaverInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.08.18
 * Time: 13:46
 */

namespace Pyz\Zed\SoftwarePackage\Business\Model\Saver;


use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;

interface MerchantSaverInterface
{
    /**
     * @param SpyMerchant $merchantEntity
     * @param MerchantTransfer $merchantTransfer
     * @return void
     */
    public function saveSoftwarePackageInMerchant(SpyMerchant $merchantEntity, MerchantTransfer $merchantTransfer);
}