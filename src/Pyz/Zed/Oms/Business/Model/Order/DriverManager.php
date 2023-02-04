<?php
/**
 * Durst - project - DriverManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.07.20
 * Time: 13:41
 */

namespace Pyz\Zed\Oms\Business\Model\Order;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class DriverManager implements DriverManagerInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * DriverManager constructor.
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idDriver
     */
    public function addDriverToOrder(
        OrderTransfer $orderTransfer,
        int $idDriver
    ): void
    {
        $orderTransfer
            ->setFkDriver(
                $idDriver
            );

        $this
            ->salesFacade
            ->updateOrder(
                $orderTransfer,
                $orderTransfer
                    ->getIdSalesOrder()
            );
    }
}
