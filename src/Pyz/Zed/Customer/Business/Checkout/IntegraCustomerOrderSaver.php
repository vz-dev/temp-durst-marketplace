<?php
/**
 * Durst - project - IntegraCustomerOrderSaver.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.20
 * Time: 13:31
 */

namespace Pyz\Zed\Customer\Business\Checkout;

use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class IntegraCustomerOrderSaver implements IntegraCustomerOrderSaverInterface
{
    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * IntegraCustomerOrderSaver constructor.
     * @param SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(
        SalesQueryContainerInterface $salesQueryContainer
    )
    {
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void
    {
        $salesOrderEntity = $this
            ->getOrderEntity(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $salesOrderEntity
            ->setIntegraCustomerNo(
                $customerId
            )
            ->save();
    }

    /**
     * @param int $orderId
     * @return SpySalesOrder
     * @throws AmbiguousComparisonException
     */
    protected function getOrderEntity(int $orderId) : SpySalesOrder
    {
        return $this
            ->salesQueryContainer
            ->querySalesOrder()
            ->filterByIdSalesOrder($orderId)
            ->findOne();
    }
}
