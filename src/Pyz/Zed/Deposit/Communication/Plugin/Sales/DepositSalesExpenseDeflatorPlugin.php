<?php
/**
 * Durst - project - DepositSalesExpenseDeflatorPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-04
 * Time: 16:06
 */

namespace Pyz\Zed\Deposit\Communication\Plugin\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class DepositSalesExpenseDeflatorPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin\Sales
 *  * @method DepositFacadeInterface getFacade()
 */
class DepositSalesExpenseDeflatorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    /**
     * Specification:
     *   - Its a plugin which hydrates OrderTransfer when order read is persistence,
     *   - is used to deflate deposit sales expenses back to single expense with quantity for each deposit item
     *
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
            ->deflateDepositSalesExpenses($orderTransfer);
    }

}