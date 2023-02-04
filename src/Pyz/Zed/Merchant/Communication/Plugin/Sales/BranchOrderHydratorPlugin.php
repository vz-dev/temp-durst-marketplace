<?php
/**
 * Durst - project - BranchOrderHydratorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 16:20
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class BranchOrderHydratorPlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\Sales
 * @method \Pyz\Zed\Merchant\Business\MerchantFacadeInterface getFacade()
 */
class BranchOrderHydratorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->hydrateOrderByBranch($orderTransfer);
    }
}
