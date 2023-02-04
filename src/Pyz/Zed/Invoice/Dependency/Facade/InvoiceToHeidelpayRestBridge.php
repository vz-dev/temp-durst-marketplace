<?php
/**
 * Durst - project - InvoiceToHeidelpayRestBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 16:08
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;


use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;

class InvoiceToHeidelpayRestBridge implements InvoiceToHeidelpayRestBridgeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface
     */
    protected $heidelpayRestFacade;

    /**
     * InvoiceToHeidelpayRestBridge constructor.
     * @param \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface $heidelpayRestFacade
     */
    public function __construct(
        HeidelpayRestFacadeInterface $heidelpayRestFacade
    )
    {
        $this->heidelpayRestFacade = $heidelpayRestFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer|null
     */
    public function getHeidelpayRestLogByIdSalesOrderAndTransactionType(
        int $idSalesOrder,
        string $transactionType
    ): ?HeidelpayRestLogTransfer
    {
        return $this
            ->heidelpayRestFacade
            ->getHeidelpayRestLogByIdSalesOrderAndTransactionType(
                $idSalesOrder,
                $transactionType
            );
    }
}
