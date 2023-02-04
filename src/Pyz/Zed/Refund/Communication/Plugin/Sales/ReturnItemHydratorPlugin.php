<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-20
 * Time: 13:43
 */

namespace Pyz\Zed\Refund\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class ReturnItemHydratorPlugin
 * @package Pyz\Zed\Refund\Communication\Plugin\Sales
 * @method RefundFacadeInterface getFacade()
 */
class ReturnItemHydratorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{

    /**
     * Specification:
     *   - sets a flag that shows if any refunds exist for the given order
     *   - sets a flag that shows if any order items with a delivery status other than missing exist for the given order
     *   - Can be used to add additional data to OrderTransfer
     *
     * @api
     *
     * @param OrderTransfer $orderTransfer
     *
     * @return OrderTransfer
     *
     * @throws AmbiguousComparisonException
     */
    public function hydrate(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer = $this
            ->getFacade()
            ->hydrateSalesOrderReturnItemFlag($orderTransfer);

        return $this
            ->getFacade()
            ->hydrateSalesOrderOtherThanMissingReturnItemFlag($orderTransfer);
    }
}
