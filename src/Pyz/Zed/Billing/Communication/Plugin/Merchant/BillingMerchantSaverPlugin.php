<?php
/**
 * Durst - project - BillingMerchantSaverPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-28
 * Time: 12:54
 */

namespace Pyz\Zed\Billing\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BillingMerchantSaverPlugin
 * @package Pyz\Zed\Billing\Communication\Plugin\Merchant
 * @method BillingFacadeInterface getFacade()
 */
class BillingMerchantSaverPlugin extends AbstractPlugin implements MerchantSaverPluginInterface
{
    /**
     * Hydrates the entity object with additional data for the given merchant transfer.
     *
     * @param SpyMerchant $entity
     * @param MerchantTransfer $transfer
     * @return void
     */
    public function saveMerchant(SpyMerchant $entity, MerchantTransfer $transfer): void
    {
        $entity
            ->setBillingPeriodPerBranch($transfer->getBillingPeriodPerBranch());
    }

}
