<?php
/**
 * Durst - project - BranchIsActivePreCheckPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:58
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Generated\Shared\Transfer\CartChangeTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Cart\Dependency\CartPreCheckPluginInterface;
use Spryker\Zed\Cart\Dependency\TerminationAwareCartPreCheckPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchIsActivePreCheckPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin
 *
 * @method MerchantFacadeInterface getFacade()
 */
class BranchIsActivePreCheckPlugin extends AbstractPlugin implements CartPreCheckPluginInterface, TerminationAwareCartPreCheckPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function check(CartChangeTransfer $cartChangeTransfer)
    {
        return $this
            ->getFacade()
            ->validateBranch(
                $cartChangeTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function terminateOnFailure(): bool
    {
        return true;
    }
}
