<?php
/**
 * Durst - project - SendInvoiceSepa.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 07.11.19
 * Time: 13:54
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceSepaMailTypePlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class SendInvoiceSepa
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method \Pyz\Zed\Oms\Business\OmsFacadeInterface getFacade()
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 */
class SendInvoiceSepa extends AbstractCommand implements CommandByOrderInterface
{
    /**
     * @param array $orderItems
     * @param SpySalesOrder $orderEntity
     * @param ReadOnlyArrayObject $data
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($orderEntity->getFkBranch());

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($orderEntity->getIdSalesOrder());

        /**
         * @deprecated
         */
        if($this->isOldWholeSaleProcess($orderEntity) !== true){
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->getDeflatedOrderByIdSalesOrder($orderEntity->getIdSalesOrder());
        }

        $this
            ->getFacade()
            ->sendInvoiceMail(
                $orderTransfer,
                $branchTransfer,
                MerchantOrderInvoiceSepaMailTypePlugin::MAIL_TYPE,
                $this->createMailTransfer(
                    $orderEntity
                        ->getIdSalesOrder()
                )
            );

        $refundTransfers = $this
            ->getFactory()
            ->getRefundFacade()
            ->getSalesOrderRefundsBySalesOrderIds([$orderEntity->getIdSalesOrder()]);

        $excludeMissingItemReturns = $branchTransfer->getEdiExcludeMissingItemReturns();

        if (($excludeMissingItemReturns === false && count($refundTransfers) > 0)
            || ($excludeMissingItemReturns === true && $orderTransfer->getHasOtherThanMissingReturnItem() === true)
        ) {
            $driverTransfer =$this
                ->getFactory()
                ->getDriverFacade()
                ->getDriverById($orderTransfer->getFkDriver());

            $this
                ->getFacade()
                ->sendRefundMail($orderTransfer, $branchTransfer, $driverTransfer);
        }

        return [];
    }

    /**
     * @deprecated
     * @param SpySalesOrder $order
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function isOldWholeSaleProcess(SpySalesOrder $order) : bool
    {
        $orderTransfer = $this->getFactory()->getSalesFacade()->getOrderByIdSalesOrder($order->getIdSalesOrder());
        foreach ($orderTransfer->getItems() as $item){
            if(in_array($item->getProcess(), $this->getFactory()->getConfig()->getOldWholesaleProcess()) === true){
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(int $idSalesOrder): MailTransfer
    {
        $deliveryTime = $this
            ->getFacade()
            ->getDeliveryTimeFromTransitionLogByIdSalesOrder(
                $idSalesOrder
            );

        return (new MailTransfer())
            ->setDeliveryTime(
                $deliveryTime
            );
    }
}
