<?php
/**
 * Durst - project - SendInvoice.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.07.18
 * Time: 15:24
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder;

use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceMailTypePlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class SendInvoice
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder
 * @method \Pyz\Zed\Oms\Business\OmsFacadeInterface getFacade()
 * @method \Pyz\Zed\Oms\Communication\OmsCommunicationFactory getFactory()
 */
class SendInvoice extends AbstractCommand implements CommandByOrderInterface
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
                MerchantOrderInvoiceMailTypePlugin::MAIL_TYPE,
                $this->createMailTransfer(
                    $orderEntity
                        ->getIdSalesOrder()
                )
            );

        $excludeMissingItemReturns = $branchTransfer->getEdiExcludeMissingItemReturns();

        if (($excludeMissingItemReturns === false && $orderEntity->getSpyRefunds()->count() > 0)
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
     * @param SpySalesOrder $order
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function isOldWholeSaleProcess(SpySalesOrder $order) : bool
    {
        foreach ($order->getItems() as $item){
            if(in_array($item->getProcess()->getName(), $this->getFactory()->getConfig()->getOldWholesaleProcess()) === true){
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
