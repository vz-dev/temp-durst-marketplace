<?php
/**
 * Durst - merchant_center - InvoiceManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.08.18
 * Time: 13:49
 */

namespace Pyz\Zed\Oms\Business\Model;

use DateTime;
use Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class InvoiceManager implements InvoiceManagerInterface
{
    public const TIME_FORMAT = DateTime::ATOM;

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface
     */
    protected $invoiceFacade;

    /**
     * InvoiceManager constructor.
     *
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     * @param \Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface $invoiceFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade,
        OmsToInvoiceBridgeInterface $invoiceFacade
    ) {
        $this->salesFacade = $salesFacade;
        $this->invoiceFacade = $invoiceFacade;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function addInvoiceNumberToOrder(int $idSalesOrder) : bool
    {
        $orderTransfer = $this
            ->salesFacade
            ->getOrderByIdSalesOrder($idSalesOrder);

        $orderTransfer
            ->setInvoiceReference(
                $this
                ->invoiceFacade
                ->createInvoiceReference($orderTransfer)
            )
            ->setInvoiceCreatedAt(
                (new DateTime('now'))
                ->format(static::TIME_FORMAT)
            );

        return $this
            ->salesFacade
            ->updateOrder($orderTransfer, $idSalesOrder);
    }
}
