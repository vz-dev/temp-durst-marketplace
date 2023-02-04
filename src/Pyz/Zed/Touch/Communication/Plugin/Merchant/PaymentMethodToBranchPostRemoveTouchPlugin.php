<?php
/**
 * Durst - project - PaymentMethodToBranchPostRemoveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 21:24
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Merchant;

use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethod;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostRemovePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PaymentMethodToBranchPostRemoveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Merchant
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PaymentMethodToBranchPostRemoveTouchPlugin extends AbstractPlugin implements PaymentMethodToBranchPostRemovePluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethod $branchToPaymentMethod
     *
     * @return void
     */
    public function remove(SpyBranchToPaymentMethod $branchToPaymentMethod): void
    {
        $this->getFacade()->touchActive(MerchantConstants::RESOURCE_TYPE_BRANCH, $branchToPaymentMethod->getFkBranch());
    }
}
