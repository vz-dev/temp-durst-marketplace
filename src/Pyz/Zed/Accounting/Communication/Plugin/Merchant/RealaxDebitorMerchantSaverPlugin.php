<?php
/**
 * Durst - project - RealaxDebitorMerchantSaverPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.04.20
 * Time: 09:16
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\Merchant;

use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchant;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantSaverPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

class RealaxDebitorMerchantSaverPlugin extends AbstractPlugin implements MerchantSaverPluginInterface
{

    /**
     * Hydrates the entity object with additional data for the given merchant transfer.
     *
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchant $entity
     * @param \Generated\Shared\Transfer\MerchantTransfer $transfer
     * @return void
     */
    public function saveMerchant(SpyMerchant $entity, MerchantTransfer $transfer): void
    {
        $entity
            ->setRealaxDebitor($transfer->getRealaxDebitor());
    }
}
