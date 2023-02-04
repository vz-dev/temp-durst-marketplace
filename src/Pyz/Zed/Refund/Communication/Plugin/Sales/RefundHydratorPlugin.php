<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-18
 * Time: 12:40
 */

namespace Pyz\Zed\Refund\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Refund\Business\RefundFacade;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class RefundHydratorPlugin
 * @package Pyz\Zed\Refund\Communication\Plugin\Sales
 * @method RefundFacade getFacade()
 */
class RefundHydratorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{

    /**
     * Specification:
     *   - Its a plugin which hydrates OrderTransfer when order read is persistence,
     *   - Can be used to add additional data to OrderTransfer
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFacade()
            ->hydrateSalesOrderRefundInformation($orderTransfer);
    }
}