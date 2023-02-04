<?php
/**
 * Durst - project - CancelOrderSalesOrderHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:19
 */

namespace Pyz\Zed\CancelOrder\Business\Hydrator;

use Generated\Shared\Transfer\CancelOrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

/**
 * Class CancelOrderSalesOrderHydrator
 * @package Pyz\Zed\CancelOrder\Business\Hydrator
 */
class CancelOrderSalesOrderHydrator implements CancelOrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade
    )
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CancelOrderTransfer $orderTransfer
     * @return void
     */
    public function hydrateCancelOrder(
        CancelOrderTransfer $orderTransfer
    ): void
    {
        $salesOrder = $this
            ->salesFacade
            ->getOrderByIdSalesOrder(
                $orderTransfer
                    ->getFkSalesOrder()
            );

        $orderTransfer
            ->setSalesOrder(
                $salesOrder
            )
            ->setBillingAddress(
                $salesOrder
                    ->getBillingAddress()
            )
            ->setShippingAddress(
                $salesOrder
                    ->getShippingAddress()
            );
    }
}
